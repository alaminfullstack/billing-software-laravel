<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CashFlowTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'transaction_date',
        'type',
        'description',
        'amount',
        'currency_code',
        'exchange_rate',
        'base_currency_amount',
        'transactionable_type',
        'transactionable_id',
    ];

    protected $casts = [
        'transaction_date' => 'date',
        'amount' => 'decimal:2',
        'exchange_rate' => 'decimal:6',
        'base_currency_amount' => 'decimal:2',
    ];

    // Relationships
    public function currency()
    {
        return $this->belongsTo(Currency::class, 'currency_code', 'code');
    }

    public function transactionable()
    {
        return $this->morphTo();
    }

    // Accessors
    public function getFormattedAmountAttribute()
    {
        return number_format($this->amount, 2);
    }

    public function getFormattedBaseAmountAttribute()
    {
        return number_format($this->base_currency_amount, 2);
    }

    public function getFormattedExchangeRateAttribute()
    {
        return number_format($this->exchange_rate, 6);
    }

    public function getTypeNameAttribute()
    {
        $types = [
            'operating' => 'Operating Activities',
            'investing' => 'Investing Activities',
            'financing' => 'Financing Activities',
        ];

        return $types[$this->type] ?? ucfirst($this->type);
    }

    // Scopes
    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('transaction_date', [$startDate, $endDate]);
    }

    public function scopeOperating($query)
    {
        return $query->where('type', 'operating');
    }

    public function scopeInvesting($query)
    {
        return $query->where('type', 'investing');
    }

    public function scopeFinancing($query)
    {
        return $query->where('type', 'financing');
    }

    // Helper methods
    public static function createFromInvoice(Invoice $invoice, $type = 'operating')
    {
        $baseCurrency = Currency::getBaseCurrency();
        $exchangeRate = $invoice->exchange_rate ?: 1;
        $baseAmount = $invoice->total_amount * $exchangeRate;

        return static::create([
            'transaction_date' => $invoice->issue_date,
            'type' => $type,
            'description' => 'Invoice #' . $invoice->invoice_number . ' - ' . $invoice->customer->name,
            'amount' => $invoice->total_amount,
            'currency_code' => $invoice->currency_code,
            'exchange_rate' => $exchangeRate,
            'base_currency_amount' => $baseAmount,
            'transactionable_type' => 'App\Models\Invoice',
            'transactionable_id' => $invoice->id,
        ]);
    }

    public static function createFromPayment(Payment $payment, $type = 'operating')
    {
        $baseCurrency = Currency::getBaseCurrency();
        $exchangeRate = $payment->exchange_rate ?: 1;
        $baseAmount = $payment->amount * $exchangeRate;

        return static::create([
            'transaction_date' => $payment->payment_date,
            'type' => $type,
            'description' => 'Payment for Invoice #' . $payment->invoice->invoice_number,
            'amount' => $payment->amount,
            'currency_code' => $payment->currency_code,
            'exchange_rate' => $exchangeRate,
            'base_currency_amount' => $baseAmount,
            'transactionable_type' => 'App\Models\Payment',
            'transactionable_id' => $payment->id,
        ]);
    }

    public static function createFromExpense(Expense $expense, $type = 'operating')
    {
        $baseCurrency = Currency::getBaseCurrency();
        $exchangeRate = $expense->exchange_rate ?: 1;
        $baseAmount = $expense->amount * $exchangeRate;

        $description = 'Expense - ' . $expense->description;
        if ($expense->supplier) {
            $description .= ' (' . $expense->supplier->name . ')';
        }

        return static::create([
            'transaction_date' => $expense->expense_date,
            'type' => $type,
            'description' => $description,
            'amount' => $expense->amount,
            'currency_code' => $expense->currency_code,
            'exchange_rate' => $exchangeRate,
            'base_currency_amount' => $baseAmount,
            'transactionable_type' => 'App\Models\Expense',
            'transactionable_id' => $expense->id,
        ]);
    }

    public static function getCashFlowStatement($startDate, $endDate, $currencyCode = 'USD')
    {
        $baseCurrency = Currency::getBaseCurrency();
        $exchangeRate = $currencyCode === $baseCurrency->code ? 1 : ExchangeRate::getCurrentRate($currencyCode, $baseCurrency->code);

        $operating = static::byType('operating')
            ->byDateRange($startDate, $endDate)
            ->sum('base_currency_amount');

        $investing = static::byType('investing')
            ->byDateRange($startDate, $endDate)
            ->sum('base_currency_amount');

        $financing = static::byType('financing')
            ->byDateRange($startDate, $endDate)
            ->sum('base_currency_amount');

        $netCashFlow = $operating + $investing + $financing;

        return [
            'operating_activities' => [
                'amount' => $operating,
                'transactions' => static::byType('operating')
                    ->byDateRange($startDate, $endDate)
                    ->with('transactionable')
                    ->get(),
            ],
            'investing_activities' => [
                'amount' => $investing,
                'transactions' => static::byType('investing')
                    ->byDateRange($startDate, $endDate)
                    ->with('transactionable')
                    ->get(),
            ],
            'financing_activities' => [
                'amount' => $financing,
                'transactions' => static::byType('financing')
                    ->byDateRange($startDate, $endDate)
                    ->with('transactionable')
                    ->get(),
            ],
            'net_cash_flow' => $netCashFlow,
        ];
    }

    public static function getAgedReceivables($asOfDate = null)
    {
        $asOfDate = $asOfDate ?: now()->toDateString();
        
        $invoices = Invoice::where('status', '!=', 'paid')
            ->where('due_date', '<=', $asOfDate)
            ->with('customer')
            ->get()
            ->map(function ($invoice) use ($asOfDate) {
                $daysOverdue = now()->parse($invoice->due_date)->diffInDays(now());
                
                $agingBucket = 'current';
                if ($daysOverdue > 0 && $daysOverdue <= 30) {
                    $agingBucket = '1_30';
                } elseif ($daysOverdue > 30 && $daysOverdue <= 60) {
                    $agingBucket = '31_60';
                } elseif ($daysOverdue > 60 && $daysOverdue <= 90) {
                    $agingBucket = '61_90';
                } elseif ($daysOverdue > 90) {
                    $agingBucket = 'over_90';
                }

                return [
                    'invoice' => $invoice,
                    'customer' => $invoice->customer,
                    'amount' => $invoice->balance_amount,
                    'days_overdue' => $daysOverdue,
                    'aging_bucket' => $agingBucket,
                ];
            });

        return [
            'current' => $invoices->where('aging_bucket', 'current'),
            '1_30' => $invoices->where('aging_bucket', '1_30'),
            '31_60' => $invoices->where('aging_bucket', '31_60'),
            '61_90' => $invoices->where('aging_bucket', '61_90'),
            'over_90' => $invoices->where('aging_bucket', 'over_90'),
        ];
    }

    public static function getAgedPayables($asOfDate = null)
    {
        $asOfDate = $asOfDate ?: now()->toDateString();
        
        // This would require expense tracking with payment status
        // For now, return empty structure
        return [
            'current' => collect(),
            '1_30' => collect(),
            '31_60' => collect(),
            '61_90' => collect(),
            'over_90' => collect(),
        ];
    }
}