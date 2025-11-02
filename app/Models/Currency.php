<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Http;

class Currency extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'symbol',
        'decimal_places',
        'is_base_currency',
        'is_active',
    ];

    protected $casts = [
        'decimal_places' => 'integer',
        'is_base_currency' => 'boolean',
        'is_active' => 'boolean',
    ];

    // Relationships
    public function exchangeRatesFrom()
    {
        return $this->hasMany(ExchangeRate::class, 'from_currency');
    }

    public function exchangeRatesTo()
    {
        return $this->hasMany(ExchangeRate::class, 'to_currency');
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class, 'currency_code');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class, 'currency_code');
    }

    public function expenses()
    {
        return $this->hasMany(Expense::class, 'currency_code');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeBaseCurrency($query)
    {
        return $query->where('is_base_currency', true);
    }

    // Accessors
    public function getFormattedCodeAttribute()
    {
        return strtoupper($this->code);
    }

    public function getExchangeRateToAttribute()
    {
        return $this->getExchangeRateTo(base_currency());
    }

    public function getExchangeRateFromAttribute()
    {
        return $this->getExchangeRateFrom($this->code);
    }

    // Helper methods
    public function getExchangeRateTo($toCurrency)
    {
        if ($this->code === $toCurrency) {
            return 1.000000;
        }

        $rate = ExchangeRate::where('from_currency', $this->code)
            ->where('to_currency', $toCurrency)
            ->latest('effective_date')
            ->first();

        return $rate ? $rate->rate : 0;
    }

    public function getExchangeRateFrom($fromCurrency)
    {
        if ($this->code === $fromCurrency) {
            return 1.000000;
        }

        $rate = ExchangeRate::where('from_currency', $fromCurrency)
            ->where('to_currency', $this->code)
            ->latest('effective_date')
            ->first();

        return $rate ? (1 / $rate->rate) : 0;
    }

    public function convertTo($amount, $toCurrency)
    {
        if ($this->code === $toCurrency) {
            return $amount;
        }

        $rate = $this->getExchangeRateTo($toCurrency);
        return $amount * $rate;
    }

    public function convertFrom($amount, $fromCurrency)
    {
        if ($this->code === $fromCurrency) {
            return $amount;
        }

        $rate = $this->getExchangeRateFrom($fromCurrency);
        return $amount * $rate;
    }

    // Static methods
    public static function getBaseCurrency()
    {
        return static::baseCurrency()->first();
    }

    public static function updateExchangeRates()
    {
        $baseCurrency = static::getBaseCurrency();
        if (!$baseCurrency) {
            return false;
        }

        try {
            // Using exchangerate-api.com (free tier)
            $response = Http::get('https://api.exchangerate-api.com/v4/latest/' . $baseCurrency->code);
            
            if ($response->successful()) {
                $rates = $response->json()['rates'];
                
                foreach ($rates as $currencyCode => $rate) {
                    // Skip base currency
                    if ($currencyCode === $baseCurrency->code) {
                        continue;
                    }
                    
                    $currency = static::where('code', $currencyCode)->first();
                    if ($currency && $currency->is_active) {
                        ExchangeRate::updateOrCreate(
                            [
                                'from_currency' => $baseCurrency->code,
                                'to_currency' => $currencyCode,
                                'effective_date' => now()->toDateString(),
                            ],
                            [
                                'rate' => $rate,
                                'is_manual' => false,
                            ]
                        );
                    }
                }
                
                return true;
            }
        } catch (\Exception $e) {
            \Log::error('Failed to update exchange rates: ' . $e->getMessage());
        }
        
        return false;
    }

    // Seed data
    public static function seedDefaultCurrencies()
    {
        $currencies = [
            ['code' => 'USD', 'name' => 'US Dollar', 'symbol' => '$', 'is_base_currency' => true],
            ['code' => 'EUR', 'name' => 'Euro', 'symbol' => '€', 'is_base_currency' => false],
            ['code' => 'GBP', 'name' => 'British Pound', 'symbol' => '£', 'is_base_currency' => false],
            ['code' => 'JPY', 'name' => 'Japanese Yen', 'symbol' => '¥', 'is_base_currency' => false],
            ['code' => 'CAD', 'name' => 'Canadian Dollar', 'symbol' => 'C$', 'is_base_currency' => false],
            ['code' => 'AUD', 'name' => 'Australian Dollar', 'symbol' => 'A$', 'is_base_currency' => false],
            ['code' => 'CHF', 'name' => 'Swiss Franc', 'symbol' => 'Fr', 'is_base_currency' => false],
            ['code' => 'CNY', 'name' => 'Chinese Yuan', 'symbol' => '¥', 'is_base_currency' => false],
            ['code' => 'INR', 'name' => 'Indian Rupee', 'symbol' => '₹', 'is_base_currency' => false],
            ['code' => 'BRL', 'name' => 'Brazilian Real', 'symbol' => 'R$', 'is_base_currency' => false],
        ];

        foreach ($currencies as $currency) {
            Currency::updateOrCreate(
                ['code' => $currency['code']],
                $currency
            );
        }
    }
}