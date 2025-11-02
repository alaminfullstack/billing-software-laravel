<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExchangeRate extends Model
{
    use HasFactory;

    protected $fillable = [
        'from_currency',
        'to_currency',
        'rate',
        'effective_date',
        'is_manual',
    ];

    protected $casts = [
        'effective_date' => 'date',
        'is_manual' => 'boolean',
        'rate' => 'decimal:6',
    ];

    // Relationships
    public function fromCurrency()
    {
        return $this->belongsTo(Currency::class, 'from_currency', 'code');
    }

    public function toCurrency()
    {
        return $this->belongsTo(Currency::class, 'to_currency', 'code');
    }

    // Accessors
    public function getFormattedRateAttribute()
    {
        return number_format($this->rate, 6);
    }

    public function getFormattedInverseRateAttribute()
    {
        return $this->rate > 0 ? number_format(1 / $this->rate, 6) : '0.000000';
    }

    public function getExchangeDirectionAttribute()
    {
        return $this->from_currency . ' to ' . $this->to_currency;
    }

    // Scopes
    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('effective_date', [$startDate, $endDate]);
    }

    public function scopeLatest($query)
    {
        return $query->orderBy('effective_date', 'desc');
    }

    public function scopeManual($query)
    {
        return $query->where('is_manual', true);
    }

    public function scopeApiUpdated($query)
    {
        return $query->where('is_manual', false);
    }

    // Helper methods
    public static function getCurrentRate($fromCurrency, $toCurrency)
    {
        if ($fromCurrency === $toCurrency) {
            return 1.000000;
        }

        $rate = static::where('from_currency', $fromCurrency)
            ->where('to_currency', $toCurrency)
            ->latest('effective_date')
            ->first();

        return $rate ? $rate->rate : 0;
    }

    public static function convert($amount, $fromCurrency, $toCurrency, $effectiveDate = null)
    {
        if ($fromCurrency === $toCurrency) {
            return $amount;
        }

        $query = static::where('from_currency', $fromCurrency)
            ->where('to_currency', $toCurrency);

        if ($effectiveDate) {
            $query->where('effective_date', '<=', $effectiveDate);
        }

        $rate = $query->latest('effective_date')->first();

        return $rate ? $amount * $rate->rate : 0;
    }

    public static function getHistoricalRates($fromCurrency, $toCurrency, $startDate, $endDate)
    {
        return static::where('from_currency', $fromCurrency)
            ->where('to_currency', $toCurrency)
            ->whereBetween('effective_date', [$startDate, $endDate])
            ->orderBy('effective_date')
            ->get();
    }

    // Update specific rate manually
    public static function updateManualRate($fromCurrency, $toCurrency, $rate, $effectiveDate = null)
    {
        $date = $effectiveDate ?: now()->toDateString();
        
        return static::updateOrCreate(
            [
                'from_currency' => $fromCurrency,
                'to_currency' => $toCurrency,
                'effective_date' => $date,
            ],
            [
                'rate' => $rate,
                'is_manual' => true,
            ]
        );
    }
}