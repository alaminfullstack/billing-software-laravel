<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FinancialAccount extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'type',
        'category',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // Relationships
    public function balances()
    {
        return $this->hasMany(AccountBalance::class);
    }

    public function cashFlowTransactions()
    {
        return $this->hasMany(CashFlowTransaction::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    // Accessors
    public function getFormattedCodeAttribute()
    {
        return str_pad($this->code, 4, '0', STR_PAD_LEFT);
    }

    public function getCurrentBalanceAttribute()
    {
        $latestBalance = $this->balances()
            ->latest('balance_date')
            ->first();

        return $latestBalance ? $latestBalance->balance : 0;
    }

    public function getAccountTypeNameAttribute()
    {
        $types = [
            'asset' => 'Assets',
            'liability' => 'Liabilities', 
            'equity' => 'Equity',
            'revenue' => 'Revenue',
            'expense' => 'Expenses',
        ];

        return $types[$this->type] ?? ucfirst($this->type);
    }

    public function getNormalBalanceAttribute()
    {
        $normalBalances = [
            'asset' => 'debit',
            'liability' => 'credit',
            'equity' => 'credit',
            'revenue' => 'credit',
            'expense' => 'debit',
        ];

        return $normalBalances[$this->type] ?? 'debit';
    }

    public function getBalanceAsOf($date)
    {
        return $this->balances()
            ->where('balance_date', '<=', $date)
            ->latest('balance_date')
            ->first();
    }

    // Static methods
    public static function getAccountsByType($type)
    {
        return static::byType($type)->active()->orderBy('code')->get();
    }

    public static function seedDefaultAccounts()
    {
        $accounts = [
            // Assets (1000-1999)
            ['code' => '1000', 'name' => 'Cash', 'type' => 'asset', 'category' => 'Current Assets'],
            ['code' => '1010', 'name' => 'Checking Account', 'type' => 'asset', 'category' => 'Current Assets'],
            ['code' => '1020', 'name' => 'Savings Account', 'type' => 'asset', 'category' => 'Current Assets'],
            ['code' => '1100', 'name' => 'Accounts Receivable', 'type' => 'asset', 'category' => 'Current Assets'],
            ['code' => '1200', 'name' => 'Inventory', 'type' => 'asset', 'category' => 'Current Assets'],
            ['code' => '1300', 'name' => 'Prepaid Expenses', 'type' => 'asset', 'category' => 'Current Assets'],
            ['code' => '1500', 'name' => 'Property & Equipment', 'type' => 'asset', 'category' => 'Fixed Assets'],
            ['code' => '1600', 'name' => 'Accumulated Depreciation', 'type' => 'asset', 'category' => 'Fixed Assets'],

            // Liabilities (2000-2999)
            ['code' => '2000', 'name' => 'Accounts Payable', 'type' => 'liability', 'category' => 'Current Liabilities'],
            ['code' => '2100', 'name' => 'Accrued Expenses', 'type' => 'liability', 'category' => 'Current Liabilities'],
            ['code' => '2200', 'name' => 'Sales Tax Payable', 'type' => 'liability', 'category' => 'Current Liabilities'],
            ['code' => '2300', 'name' => 'Payroll Taxes Payable', 'type' => 'liability', 'category' => 'Current Liabilities'],
            ['code' => '2400', 'name' => 'Notes Payable', 'type' => 'liability', 'category' => 'Long-term Liabilities'],
            ['code' => '2500', 'name' => 'Loans Payable', 'type' => 'liability', 'category' => 'Long-term Liabilities'],

            // Equity (3000-3999)
            ['code' => '3000', 'name' => 'Owner\'s Equity', 'type' => 'equity', 'category' => 'Owner\'s Equity'],
            ['code' => '3100', 'name' => 'Retained Earnings', 'type' => 'equity', 'category' => 'Retained Earnings'],
            ['code' => '3200', 'name' => 'Current Year Earnings', 'type' => 'equity', 'category' => 'Current Year Earnings'],

            // Revenue (4000-4999)
            ['code' => '4000', 'name' => 'Sales Revenue', 'type' => 'revenue', 'category' => 'Operating Revenue'],
            ['code' => '4100', 'name' => 'Service Revenue', 'type' => 'revenue', 'category' => 'Operating Revenue'],
            ['code' => '4200', 'name' => 'Interest Income', 'type' => 'revenue', 'category' => 'Other Income'],
            ['code' => '4300', 'name' => 'Other Income', 'type' => 'revenue', 'category' => 'Other Income'],

            // Expenses (5000-9999)
            ['code' => '5000', 'name' => 'Cost of Goods Sold', 'type' => 'expense', 'category' => 'Cost of Sales'],
            ['code' => '6000', 'name' => 'Salaries & Wages', 'type' => 'expense', 'category' => 'Operating Expenses'],
            ['code' => '6100', 'name' => 'Rent Expense', 'type' => 'expense', 'category' => 'Operating Expenses'],
            ['code' => '6200', 'name' => 'Utilities', 'type' => 'expense', 'category' => 'Operating Expenses'],
            ['code' => '6300', 'name' => 'Office Supplies', 'type' => 'expense', 'category' => 'Operating Expenses'],
            ['code' => '6400', 'name' => 'Marketing & Advertising', 'type' => 'expense', 'category' => 'Operating Expenses'],
            ['code' => '6500', 'name' => 'Professional Services', 'type' => 'expense', 'category' => 'Operating Expenses'],
            ['code' => '6600', 'name' => 'Insurance', 'type' => 'expense', 'category' => 'Operating Expenses'],
            ['code' => '6700', 'name' => 'Depreciation Expense', 'type' => 'expense', 'category' => 'Operating Expenses'],
            ['code' => '6800', 'name' => 'Interest Expense', 'type' => 'expense', 'category' => 'Other Expenses'],
            ['code' => '6900', 'name' => 'Taxes & Licenses', 'type' => 'expense', 'category' => 'Other Expenses'],
        ];

        foreach ($accounts as $account) {
            FinancialAccount::updateOrCreate(
                ['code' => $account['code']],
                $account
            );
        }
    }
}