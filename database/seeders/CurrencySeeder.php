<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Currency;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class CurrencySeeder extends Seeder
{
    public function run()
    {
        echo "Seeding currencies and exchange rates...\n";
        
        // Create default currencies
        $currencies = [
            ['code' => 'USD', 'name' => 'US Dollar', 'symbol' => '$', 'is_base' => true, 'is_active' => true, 'exchange_rate' => 1.000000],
            ['code' => 'EUR', 'name' => 'Euro', 'symbol' => '€', 'is_base' => false, 'is_active' => true, 'exchange_rate' => 0.850000],
            ['code' => 'GBP', 'name' => 'British Pound', 'symbol' => '£', 'is_base' => false, 'is_active' => true, 'exchange_rate' => 0.750000],
            ['code' => 'JPY', 'name' => 'Japanese Yen', 'symbol' => '¥', 'is_base' => false, 'is_active' => true, 'exchange_rate' => 110.000000],
            ['code' => 'CAD', 'name' => 'Canadian Dollar', 'symbol' => 'C$', 'is_base' => false, 'is_active' => true, 'exchange_rate' => 1.250000],
            ['code' => 'AUD', 'name' => 'Australian Dollar', 'symbol' => 'A$', 'is_base' => false, 'is_active' => true, 'exchange_rate' => 1.350000],
            ['code' => 'CHF', 'name' => 'Swiss Franc', 'symbol' => 'Fr', 'is_base' => false, 'is_active' => true, 'exchange_rate' => 0.920000],
            ['code' => 'CNY', 'name' => 'Chinese Yuan', 'symbol' => '¥', 'is_base' => false, 'is_active' => true, 'exchange_rate' => 6.450000],
            ['code' => 'INR', 'name' => 'Indian Rupee', 'symbol' => '₹', 'is_base' => false, 'is_active' => true, 'exchange_rate' => 74.500000],
            ['code' => 'MXN', 'name' => 'Mexican Peso', 'symbol' => '$', 'is_base' => false, 'is_active' => true, 'exchange_rate' => 20.150000],
        ];
        
        foreach ($currencies as $currency) {
            Currency::firstOrCreate(
                ['code' => $currency['code']], 
                array_merge($currency, [
                    'rate_updated_at' => now(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
        }
        
        echo "Currency data seeded successfully!\n";
        
        // Create sample exchange rates
        $exchangeRates = [
            ['from_currency' => 'USD', 'to_currency' => 'EUR', 'rate' => 0.850000, 'effective_date' => now()->toDateString(), 'source' => 'Manual Entry'],
            ['from_currency' => 'USD', 'to_currency' => 'GBP', 'rate' => 0.750000, 'effective_date' => now()->toDateString(), 'source' => 'Manual Entry'],
            ['from_currency' => 'USD', 'to_currency' => 'JPY', 'rate' => 110.000000, 'effective_date' => now()->toDateString(), 'source' => 'Manual Entry'],
            ['from_currency' => 'USD', 'to_currency' => 'CAD', 'rate' => 1.250000, 'effective_date' => now()->toDateString(), 'source' => 'Manual Entry'],
            ['from_currency' => 'USD', 'to_currency' => 'AUD', 'rate' => 1.350000, 'effective_date' => now()->toDateString(), 'source' => 'Manual Entry'],
            ['from_currency' => 'EUR', 'to_currency' => 'USD', 'rate' => 1.176471, 'effective_date' => now()->toDateString(), 'source' => 'Calculated'],
            ['from_currency' => 'GBP', 'to_currency' => 'USD', 'rate' => 1.333333, 'effective_date' => now()->toDateString(), 'source' => 'Calculated'],
            ['from_currency' => 'JPY', 'to_currency' => 'USD', 'rate' => 0.009091, 'effective_date' => now()->toDateString(), 'source' => 'Calculated'],
            ['from_currency' => 'CAD', 'to_currency' => 'USD', 'rate' => 0.800000, 'effective_date' => now()->toDateString(), 'source' => 'Calculated'],
            ['from_currency' => 'AUD', 'to_currency' => 'USD', 'rate' => 0.740741, 'effective_date' => now()->toDateString(), 'source' => 'Calculated'],
        ];
        
        foreach ($exchangeRates as $rate) {
            \App\Models\ExchangeRate::firstOrCreate(
                [
                    'from_currency' => $rate['from_currency'],
                    'to_currency' => $rate['to_currency'],
                    'effective_date' => $rate['effective_date']
                ],
                array_merge($rate, [
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
        }
        
        echo "Exchange rates seeded successfully!\n";
    }
}