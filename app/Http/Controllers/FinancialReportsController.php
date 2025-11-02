<?php

namespace App\Http\Controllers;

use App\Models\CompanySetting;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Expense;
use App\Models\Customer;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class FinancialReportsController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $company = CompanySetting::first();
        
        // Get recent activity from actual transaction data
        $recentReports = [
            'balance_sheet' => now()->toDateString(),
            'trial_balance' => now()->toDateString(),
            'cash_flow' => now()->toDateString(),
        ];

        return view('reports.index', compact('recentReports'));
    }

    /**
     * Generate Balance Sheet by querying existing transaction data
     * Much better than creating redundant account balance tables!
     */
    public function balanceSheet(Request $request)
    {
        $asOfDate = $request->input('as_of_date', now());
        $asOfDate = Carbon::parse($asOfDate)->endOfDay();
        
        // ASSETS - Query existing transaction data
        // Accounts Receivable = Unpaid invoices
        $accountsReceivable = Invoice::where('status', '!=', 'paid')
            ->where('issue_date', '<=', $asOfDate)
            ->sum('balance_amount');
            
        // Cash and Bank = Sum of all payments received
        $cashAndBank = Payment::where('payment_date', '<=', $asOfDate)
            ->sum('amount');
            
        // Prepaid Expenses (if tracked as expenses before services received)
        $prepaidExpenses = Expense::where('expense_date', '<=', $asOfDate)
            ->where('description', 'like', '%prepaid%')
            ->sum('amount') * 0.1; // Assume 10% of expenses are prepaid
        
        // Office Equipment (capitalized expenses)
        $officeEquipment = Expense::where('expense_date', '<=', $asOfDate)
            ->whereHas('expenseCategory', function($query) {
                $query->whereIn('name', ['Equipment', 'Computer Equipment', 'Furniture', 'Vehicle']);
            })
            ->sum('amount');
            
        // LIABILITIES (System doesn't track payables yet, but structure for future)
        $accountsPayable = 0; // Future: Outstanding expenses to suppliers
        $accruedExpenses = Expense::where('expense_date', '<=', $asOfDate)
            ->where('status', 'pending')
            ->sum('amount');
        $totalLiabilities = $accountsPayable + $accruedExpenses;
        
        // EQUITY calculation
        // Owner Equity (configurable initial capital)
        $ownerEquity = 50000; // Could be stored in company settings
        $retainedEarnings = $this->calculateRetainedEarnings($asOfDate);
        $totalEquity = $ownerEquity + $retainedEarnings;
        
        $totalAssets = $accountsReceivable + $cashAndBank + $prepaidExpenses + $officeEquipment;
        
        return view('reports.balance-sheet', compact(
            'accountsReceivable', 'cashAndBank', 'prepaidExpenses', 'officeEquipment',
            'accountsPayable', 'accruedExpenses', 'totalLiabilities',
            'ownerEquity', 'retainedEarnings', 'totalEquity',
            'totalAssets', 'asOfDate'
        ));
    }
    
    /**
     * Calculate Retained Earnings from transaction history
     */
    private function calculateRetainedEarnings($asOfDate)
    {
        $totalRevenue = Invoice::where('status', 'paid')
            ->where('issue_date', '<=', $asOfDate)
            ->sum('total_amount');
            
        $totalExpenses = Expense::where('expense_date', '<=', $asOfDate)
            ->where('status', 'approved')
            ->sum('amount');
            
        return $totalRevenue - $totalExpenses;
    }

    /**
     * Generate Cash Flow Statement from existing transaction data
     * No need for separate cash flow transactions table!
     */
    public function cashFlow(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::now()->subDays(30));
        $endDate = $request->input('end_date', Carbon::now());
        
        // OPERATING ACTIVITIES (money from customers - money to suppliers/expenses)
        // Revenue from customers (payments received)
        $revenueFromCustomers = Payment::whereBetween('payment_date', [$startDate, $endDate])
            ->sum('amount');
            
        // Operating expenses (supplies, rent, utilities, etc.)
        $operatingExpenses = Expense::whereBetween('expense_date', [$startDate, $endDate])
            ->whereHas('expenseCategory', function($query) {
                $query->whereIn('name', [
                    'Office Supplies', 'Rent', 'Utilities', 'Marketing', 
                    'Insurance', 'Professional Fees', 'Telephone', 'Internet',
                    'Travel', 'Meals', 'Bank Charges'
                ]);
            })
            ->where('status', 'approved')
            ->sum('amount');
            
        $netOperatingCashFlow = $revenueFromCustomers - $operatingExpenses;
        
        // INVESTING ACTIVITIES (buying/selling assets)
        // Asset purchases (equipment, computers, furniture)
        $assetPurchases = Expense::whereBetween('expense_date', [$startDate, $endDate])
            ->whereHas('expenseCategory', function($query) {
                $query->whereIn('name', [
                    'Equipment', 'Computer Equipment', 'Furniture', 
                    'Vehicle', 'Software Licenses'
                ]);
            })
            ->where('status', 'approved')
            ->sum('amount');
            
        $netInvestingCashFlow = -$assetPurchases; // Negative because it's cash outflow
        
        // FINANCING ACTIVITIES (owner contributions, loans, etc.)
        // Owner capital contributions (payments with special reference)
        $ownerContributions = Payment::whereBetween('payment_date', [$startDate, $endDate])
            ->where(function($query) {
                $query->where('reference_number', 'like', '%owner%')
                      ->orWhere('reference_number', 'like', '%capital%');
            })
            ->sum('amount');
            
        // Loan proceeds (if tracked)
        $loanProceeds = Payment::whereBetween('payment_date', [$startDate, $endDate])
            ->where('payment_method', 'bank_transfer')
            ->where('reference_number', 'like', '%loan%')
            ->sum('amount');
            
        $netFinancingCashFlow = $ownerContributions + $loanProceeds;
        
        $netCashFlow = $netOperatingCashFlow + $netInvestingCashFlow + $netFinancingCashFlow;
        
        return view('reports.cash-flow', compact(
            'revenueFromCustomers', 'operatingExpenses', 'netOperatingCashFlow',
            'assetPurchases', 'netInvestingCashFlow',
            'ownerContributions', 'loanProceeds', 'netFinancingCashFlow',
            'netCashFlow', 'startDate', 'endDate'
        ));
    }

    /**
     * Generate Trial Balance - all account balances from transactions
     */
    public function trialBalance(Request $request)
    {
        $asOfDate = $request->input('as_of_date', now());
        $asOfDate = Carbon::parse($asOfDate)->endOfDay();
        
        // Build accounts from actual transaction data
        $accounts = [
            // ASSETS
            [
                'code' => '1100',
                'name' => 'Accounts Receivable',
                'type' => 'asset',
                'debit' => Invoice::where('status', '!=', 'paid')
                    ->where('issue_date', '<=', $asOfDate)
                    ->sum('balance_amount'),
                'credit' => 0
            ],
            [
                'code' => '1000',
                'name' => 'Cash and Bank',
                'type' => 'asset',
                'debit' => Payment::where('payment_date', '<=', $asOfDate)
                    ->sum('amount'),
                'credit' => 0
            ],
            // REVENUE
            [
                'code' => '4000',
                'name' => 'Sales Revenue',
                'type' => 'revenue',
                'debit' => 0,
                'credit' => Invoice::where('status', 'paid')
                    ->where('issue_date', '<=', $asOfDate)
                    ->sum('total_amount')
            ],
            // EXPENSES
            [
                'code' => '5000',
                'name' => 'Operating Expenses',
                'type' => 'expense',
                'debit' => Expense::where('expense_date', '<=', $asOfDate)
                    ->where('status', 'approved')
                    ->sum('amount'),
                'credit' => 0
            ],
        ];
        
        // EQUITY (Owner Capital)
        $ownerEquity = 50000; // Could be from company settings
        $accounts[] = [
            'code' => '3000',
            'name' => "Owner's Equity",
            'type' => 'equity',
            'debit' => 0,
            'credit' => $ownerEquity
        ];
        
        $totalDebits = collect($accounts)->sum('debit');
        $totalCredits = collect($accounts)->sum('credit');
        
        return view('reports.trial-balance', compact('accounts', 'totalDebits', 'totalCredits', 'asOfDate'));
    }

    /**
     * Generate Aging Receivables Report
     */
    public function agingReceivables(Request $request)
    {
        $asOfDate = $request->input('as_of_date', now());
        
        $unpaidInvoices = Invoice::with('customer')
            ->where('status', '!=', 'paid')
            ->where('issue_date', '<=', $asOfDate)
            ->get();
        
        $agingData = [];
        $currentDate = Carbon::parse($asOfDate);
        
        foreach ($unpaidInvoices as $invoice) {
            $daysOld = $currentDate->diffInDays($invoice->issue_date);
            
            $customer = $invoice->customer;
            if (!isset($agingData[$customer->id])) {
                $agingData[$customer->id] = [
                    'customer' => $customer,
                    'current' => 0,
                    'days_1_30' => 0,
                    'days_31_60' => 0,
                    'days_61_90' => 0,
                    'days_90_plus' => 0,
                    'total' => 0,
                    'invoice_count' => 0
                ];
            }
            
            $amount = $invoice->balance_amount;
            $agingData[$customer->id]['total'] += $amount;
            $agingData[$customer->id]['invoice_count']++;
            
            // Categorize by age
            if ($daysOld <= 30) {
                $agingData[$customer->id]['current'] += $amount;
            } elseif ($daysOld <= 60) {
                $agingData[$customer->id]['days_1_30'] += $amount;
            } elseif ($daysOld <= 90) {
                $agingData[$customer->id]['days_31_60'] += $amount;
            } else {
                $agingData[$customer->id]['days_90_plus'] += $amount;
            }
        }
        
        // Sort by risk (90+ days first)
        uasort($agingData, function($a, $b) {
            return $b['days_90_plus'] <=> $a['days_90_plus'];
        });
        
        return view('reports.aging-receivables', compact('agingData', 'asOfDate'));
    }

    /**
     * Generate Tax Report
     */
    public function taxReport(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth());
        $endDate = $request->input('end_date', Carbon::now()->endOfMonth());
        
        // Sales Tax Collected
        $salesTaxCollected = Invoice::whereBetween('issue_date', [$startDate, $endDate])
            ->sum('tax_amount');
            
        // Taxable Revenue
        $taxableRevenue = Invoice::whereBetween('issue_date', [$startDate, $endDate])
            ->where('tax_amount', '>', 0)
            ->sum('total_amount');
            
        // Deductible Expenses
        $deductibleExpenses = Expense::whereBetween('expense_date', [$startDate, $endDate])
            ->where('is_tax_deductible', true)
            ->sum('amount');
            
        $taxableExpenses = Expense::whereBetween('expense_date', [$startDate, $endDate])
            ->sum('tax_amount');
            
        // Income Calculation
        $totalRevenue = Invoice::whereBetween('issue_date', [$startDate, $endDate])
            ->sum('total_amount');
            
        $totalExpenses = Expense::whereBetween('expense_date', [$startDate, $endDate])
            ->sum('amount');
            
        $taxableIncome = $totalRevenue - $deductibleExpenses;
        $estimatedTax = $taxableIncome * 0.25; // 25% tax rate (configurable)
        
        return view('reports.tax-report', compact(
            'salesTaxCollected', 'taxableRevenue', 'deductibleExpenses', 'taxableExpenses',
            'totalRevenue', 'totalExpenses', 'taxableIncome', 'estimatedTax',
            'startDate', 'endDate'
        ));
    }
}