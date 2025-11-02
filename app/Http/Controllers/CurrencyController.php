<?php

namespace App\Http\Controllers;

use App\Models\Currency;
use App\Models\ExchangeRate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class CurrencyController extends Controller
{
    public function index()
    {
        $currencies = Currency::active()->orderBy('is_base_currency', 'desc')->orderBy('code')->get();
        $baseCurrency = Currency::getBaseCurrency();
        
        return view('currencies.index', compact('currencies', 'baseCurrency'));
    }

    /**
     * Show currency management form
     */
    public function create()
    {
        $baseCurrency = Currency::getBaseCurrency();
        return view('currencies.create', compact('baseCurrency'));
    }

    /**
     * Store new currency
     */
    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|string|size:3|unique:currencies,code',
            'name' => 'required|string|max:255',
            'symbol' => 'required|string|max:10',
            'decimal_places' => 'required|integer|min:0|max:6',
            'is_base_currency' => 'boolean',
            'is_active' => 'boolean',
        ]);

        try {
            // If setting as base currency, unset other base currencies
            if ($request->is_base_currency) {
                Currency::where('is_base_currency', true)->update(['is_base_currency' => false]);
            }

            Currency::create($request->all());

            return redirect()->route('currencies.index')
                ->with('success', 'Currency created successfully.');
        } catch (\Exception $e) {
            return back()->withInput()
                ->withError('Error creating currency: ' . $e->getMessage());
        }
    }

    /**
     * Edit currency
     */
    public function edit(Currency $currency)
    {
        $baseCurrency = Currency::getBaseCurrency();
        return view('currencies.edit', compact('currency', 'baseCurrency'));
    }

    /**
     * Update currency
     */
    public function update(Request $request, Currency $currency)
    {
        $request->validate([
            'code' => 'required|string|size:3|unique:currencies,code,' . $currency->id,
            'name' => 'required|string|max:255',
            'symbol' => 'required|string|max:10',
            'decimal_places' => 'required|integer|min:0|max:6',
            'is_base_currency' => 'boolean',
            'is_active' => 'boolean',
        ]);

        try {
            // If setting as base currency, unset other base currencies
            if ($request->is_base_currency && !$currency->is_base_currency) {
                Currency::where('is_base_currency', true)->update(['is_base_currency' => false]);
            }

            $currency->update($request->all());

            return redirect()->route('currencies.index')
                ->with('success', 'Currency updated successfully.');
        } catch (\Exception $e) {
            return back()->withInput()
                ->withError('Error updating currency: ' . $e->getMessage());
        }
    }

    /**
     * Delete currency
     */
    public function destroy(Currency $currency)
    {
        try {
            // Cannot delete base currency
            if ($currency->is_base_currency) {
                return back()->withError('Cannot delete base currency.');
            }

            // Check if currency is in use
            if ($currency->invoices()->count() > 0 || 
                $currency->payments()->count() > 0 || 
                $currency->expenses()->count() > 0) {
                return back()->withError('Cannot delete currency that is in use by transactions.');
            }

            $currency->delete();

            return redirect()->route('currencies.index')
                ->with('success', 'Currency deleted successfully.');
        } catch (\Exception $e) {
            return back()->withError('Error deleting currency: ' . $e->getMessage());
        }
    }

    /**
     * Exchange Rates Management
     */
    public function exchangeRates(Request $request)
    {
        $currencies = Currency::active()->orderBy('code')->get();
        $baseCurrency = Currency::getBaseCurrency();
        
        // Get latest exchange rates
        $exchangeRates = ExchangeRate::with(['fromCurrency', 'toCurrency'])
            ->latest('effective_date')
            ->take(50)
            ->get();

        // Group by currency pair
        $ratesByPair = $exchangeRates->groupBy(function ($rate) {
            return $rate->from_currency . '-' . $rate->to_currency;
        });

        return view('currencies.exchange-rates', compact('currencies', 'baseCurrency', 'ratesByPair'));
    }

    /**
     * Update exchange rates from API
     */
    public function updateExchangeRates()
    {
        try {
            $success = Currency::updateExchangeRates();
            
            if ($success) {
                return back()->with('success', 'Exchange rates updated successfully.');
            } else {
                return back()->withError('Failed to update exchange rates. Please try again later.');
            }
        } catch (\Exception $e) {
            return back()->withError('Error updating exchange rates: ' . $e->getMessage());
        }
    }

    /**
     * Manual exchange rate update form
     */
    public function editExchangeRate(Request $request)
    {
        $request->validate([
            'from_currency' => 'required|string|size:3',
            'to_currency' => 'required|string|size:3|different:from_currency',
            'effective_date' => 'required|date',
        ]);

        $fromCurrency = Currency::where('code', $request->from_currency)->first();
        $toCurrency = Currency::where('code', $request->to_currency)->first();
        
        if (!$fromCurrency || !$toCurrency) {
            return back()->withError('Invalid currency codes.');
        }

        $existingRate = ExchangeRate::where('from_currency', $request->from_currency)
            ->where('to_currency', $request->to_currency)
            ->where('effective_date', $request->effective_date)
            ->first();

        return view('currencies.edit-exchange-rate', compact(
            'fromCurrency', 
            'toCurrency', 
            'existingRate'
        ));
    }

    /**
     * Update manual exchange rate
     */
    public function updateExchangeRate(Request $request)
    {
        $request->validate([
            'from_currency' => 'required|string|size:3',
            'to_currency' => 'required|string|size:3|different:from_currency',
            'effective_date' => 'required|date',
            'rate' => 'required|numeric|min:0.000001',
        ]);

        try {
            ExchangeRate::updateManualRate(
                $request->from_currency,
                $request->to_currency,
                $request->rate,
                $request->effective_date
            );

            return redirect()->route('currencies.exchange-rates')
                ->with('success', 'Exchange rate updated successfully.');
        } catch (\Exception $e) {
            return back()->withInput()
                ->withError('Error updating exchange rate: ' . $e->getMessage());
        }
    }

    /**
     * Currency conversion calculator
     */
    public function convert(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0',
            'from_currency' => 'required|string|size:3',
            'to_currency' => 'required|string|size:3|different:from_currency',
        ]);

        $fromCurrency = Currency::where('code', $request->from_currency)->first();
        $toCurrency = Currency::where('code', $request->to_currency)->first();

        if (!$fromCurrency || !$toCurrency) {
            return back()->withError('Invalid currency codes.');
        }

        $convertedAmount = $fromCurrency->convertTo($request->amount, $request->to_currency);
        $exchangeRate = ExchangeRate::getCurrentRate($request->from_currency, $request->to_currency);

        return back()->with([
            'conversion_result' => [
                'amount' => $request->amount,
                'from_currency' => $fromCurrency,
                'to_currency' => $toCurrency,
                'converted_amount' => $convertedAmount,
                'exchange_rate' => $exchangeRate,
            ]
        ]);
    }

    /**
     * Historical exchange rates
     */
    public function historicalRates(Request $request)
    {
        $request->validate([
            'from_currency' => 'required|string|size:3',
            'to_currency' => 'required|string|size:3|different:from_currency',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        try {
            $rates = ExchangeRate::getHistoricalRates(
                $request->from_currency,
                $request->to_currency,
                $request->start_date,
                $request->end_date
            );

            $fromCurrency = Currency::where('code', $request->from_currency)->first();
            $toCurrency = Currency::where('code', $request->to_currency)->first();

            return view('currencies.historical-rates', compact(
                'rates',
                'fromCurrency',
                'toCurrency',
                'request'
            ));

        } catch (\Exception $e) {
            return back()->withError('Error fetching historical rates: ' . $e->getMessage());
        }
    }

    /**
     * Currency settings
     */
    public function settings()
    {
        $baseCurrency = Currency::getBaseCurrency();
        $currencies = Currency::active()->orderBy('is_base_currency', 'desc')->orderBy('code')->get();
        
        return view('currencies.settings', compact('baseCurrency', 'currencies'));
    }

    /**
     * Update currency settings
     */
    public function updateSettings(Request $request)
    {
        $request->validate([
            'base_currency' => 'required|string|size:3|exists:currencies,code',
            'auto_update_rates' => 'boolean',
            'rate_update_frequency' => 'required_if:auto_update_rates,1|in:hourly,daily,weekly',
        ]);

        try {
            // Update base currency
            Currency::where('is_base_currency', true)->update(['is_base_currency' => false]);
            Currency::where('code', $request->base_currency)->update(['is_base_currency' => true]);

            // Store other settings (could be in a settings table)
            // For now, just return success
            session(['currency_settings' => $request->only(['auto_update_rates', 'rate_update_frequency'])]);

            return back()->with('success', 'Currency settings updated successfully.');
        } catch (\Exception $e) {
            return back()->withError('Error updating settings: ' . $e->getMessage());
        }
    }

    /**
     * Seed default currencies and accounts
     */
    public function seedDefaults()
    {
        try {
            Currency::seedDefaultCurrencies();
            \App\Models\FinancialAccount::seedDefaultAccounts();
            
            return back()->with('success', 'Default currencies and financial accounts created successfully.');
        } catch (\Exception $e) {
            return back()->withError('Error seeding defaults: ' . $e->getMessage());
        }
    }
}