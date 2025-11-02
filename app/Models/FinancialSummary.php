<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FinancialSummary extends Model
{
    use HasFactory;

    protected $fillable = [
        'month',
        'year',
        'total_revenue',
        'total_expenses',
        'net_profit',
    ];

    protected $casts = [
        'month' => 'integer',
        'year' => 'integer',
        'total_revenue' => 'decimal:2',
        'total_expenses' => 'decimal:2',
        'net_profit' => 'decimal:2',
    ];

    // Accessors
    public function getFormattedRevenueAttribute()
    {
        return number_format($this->total_revenue, 2);
    }

    public function getFormattedExpensesAttribute()
    {
        return number_format($this->total_expenses, 2);
    }

    public function getFormattedProfitAttribute()
    {
        return number_format($this->net_profit, 2);
    }

    public function getFormattedPeriodAttribute()
    {
        $months = [
            1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April',
            5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August',
            9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December',
        ];

        return $months[$this->month] . ' ' . $this->year;
    }

    // Scopes
    public function scopeCurrentYear($query)
    {
        return $query->where('year', date('Y'));
    }

    public function scopeByPeriod($query, $month, $year)
    {
        return $query->where('month', $month)->where('year', $year);
    }
}
