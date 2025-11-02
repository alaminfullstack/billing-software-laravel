<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccountBalance extends Model
{
    use HasFactory;

    protected $fillable = [
        'financial_account_id',
        'balance_date',
        'debit_balance',
        'credit_balance',
        'currency_code',
        'base_currency_debit',
        'base_currency_credit',
    ];

    protected $casts = [
        'balance_date' => 'date',
        'debit_balance' => 'decimal:2',
        'credit_balance' => 'decimal:2',
        'base_currency_debit' => 'decimal:2',
        'base_currency_credit' => 'decimal:2',
    ];

    // Relationships
    public function account()
    {
        return $this->belongsTo(FinancialAccount::class, 'financial_account_id');
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class, 'currency_code', 'code');
    }

    // Accessors
    public function getNetBalanceAttribute()
    {
        return $this->debit_balance - $this->credit_balance;
    }

    public function getFormattedDebitAttribute()
    {
        return number_format($this->debit_balance, 2);
    }

    public function getFormattedCreditAttribute()
    {
        return number_format($this->credit_balance, 2);
    }

    public function getFormattedNetAttribute()
    {
        return number_format($this->net_balance, 2);
    }

    public function getBaseCurrencyNetAttribute()
    {
        return $this->base_currency_debit - $this->base_currency_credit;
    }

    public function getFormattedBaseNetAttribute()
    {
        return number_format($this->base_currency_net, 2);
    }

    public function getBalanceDirectionAttribute()
    {
        if ($this->net_balance > 0) {
            return 'Debit';
        } elseif ($this->net_balance < 0) {
            return 'Credit';
        }
        return 'Zero';
    }

    // Scopes
    public function scopeAsOfDate($query, $date)
    {
        return $query->where('balance_date', '<=', $date);
    }

    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('balance_date', [$startDate, $endDate]);
    }

    public function scopeForAccount($query, $accountId)
    {
        return $query->where('financial_account_id', $accountId);
    }

    public function scopeForCurrency($query, $currencyCode)
    {
        return $query->where('currency_code', $currencyCode);
    }

    public function scopeNonZero($query)
    {
        return $query->where(function ($q) {
            $q->where('debit_balance', '>', 0)
              ->orWhere('credit_balance', '>', 0);
        });
    }

    // Helper methods
    public static function updateBalance($accountId, $date, $debitAmount = 0, $creditAmount = 0, $currencyCode = 'USD')
    {
        $baseCurrency = Currency::getBaseCurrency();
        $exchangeRate = ExchangeRate::getCurrentRate($currencyCode, $baseCurrency->code);
        
        $baseDebit = $debitAmount * $exchangeRate;
        $baseCredit = $creditAmount * $exchangeRate;

        return static::updateOrCreate(
            [
                'financial_account_id' => $accountId,
                'balance_date' => $date,
                'currency_code' => $currencyCode,
            ],
            [
                'debit_balance' => $debitAmount,
                'credit_balance' => $creditAmount,
                'base_currency_debit' => $baseDebit,
                'base_currency_credit' => $baseCredit,
            ]
        );
    }

    public static function getTrialBalance($date, $currencyCode = 'USD')
    {
        return static::where('balance_date', '<=', $date)
            ->with('account')
            ->get()
            ->groupBy('financial_account_id')
            ->map(function ($balances) use ($currencyCode) {
                return [
                    'account' => $balances->first()->account,
                    'debit_balance' => $balances->sum('debit_balance'),
                    'credit_balance' => $balances->sum('credit_balance'),
                    'base_currency_debit' => $balances->sum('base_currency_debit'),
                    'base_currency_credit' => $balances->sum('base_currency_credit'),
                ];
            });
    }

    public static function getBalanceSheet($date, $currencyCode = 'USD')
    {
        $baseCurrency = Currency::getBaseCurrency();
        
        $assets = static::where('balance_date', '<=', $date)
            ->whereHas('account', function ($query) {
                $query->where('type', 'asset');
            })
            ->with('account')
            ->get()
            ->groupBy('financial_account_id')
            ->map(function ($balances) use ($currencyCode) {
                $account = $balances->first()->account;
                $totalDebit = $balances->sum('debit_balance');
                $totalCredit = $balances->sum('credit_balance');
                
                // Normal balance for assets is debit
                $netBalance = $totalDebit - $totalCredit;
                
                return [
                    'account' => $account,
                    'balance' => $netBalance,
                ];
            });

        $liabilities = static::where('balance_date', '<=', $date)
            ->whereHas('account', function ($query) {
                $query->where('type', 'liability');
            })
            ->with('account')
            ->get()
            ->groupBy('financial_account_id')
            ->map(function ($balances) use ($currencyCode) {
                $account = $balances->first()->account;
                $totalDebit = $balances->sum('debit_balance');
                $totalCredit = $balances->sum('credit_balance');
                
                // Normal balance for liabilities is credit
                $netBalance = $totalCredit - $totalDebit;
                
                return [
                    'account' => $account,
                    'balance' => $netBalance,
                ];
            });

        $equity = static::where('balance_date', '<=', $date)
            ->whereHas('account', function ($query) {
                $query->where('type', 'equity');
            })
            ->with('account')
            ->get()
            ->groupBy('financial_account_id')
            ->map(function ($balances) use ($currencyCode) {
                $account = $balances->first()->account;
                $totalDebit = $balances->sum('debit_balance');
                $totalCredit = $balances->sum('credit_balance');
                
                // Normal balance for equity is credit
                $netBalance = $totalCredit - $totalDebit;
                
                return [
                    'account' => $account,
                    'balance' => $netBalance,
                ];
            });

        return [
            'assets' => $assets->values(),
            'liabilities' => $liabilities->values(),
            'equity' => $equity->values(),
        ];
    }
}