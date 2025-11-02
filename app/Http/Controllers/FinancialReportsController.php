<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Expense;
use App\Models\AccountBalance;
use App\Models\FinancialAccount;
use App\Models\CashFlowTransaction;
use App\Models\Currency;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class FinancialReportsController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $company = $user->companySettings()->first();
        
        $recentReports = [
            'balance_sheet' => AccountBalance::latest('balance_date')->first(),
            'trial_balance' => AccountBalance::latest('balance_date')->first(),
            'cash_flow' => CashFlowTransaction::latest('transaction_date')->first(),
        ];

        return view('reports.index', compact('recentReports'));
    }

    /**
     * Balance Sheet Report
     */
    public function balanceSheet(Request $request)
    {
        $request->validate([
            'as_of_date' => 'required|date',
            'currency' => 'nullable|string|size:3',
        ]);

        $asOfDate = $request->as_of_date;
        $currencyCode = $request->currency ?: 'USD';
        
        try {
            $balanceSheet = AccountBalance::getBalanceSheet($asOfDate, $currencyCode);
            
            $totalAssets = $balanceSheet['assets']->sum('balance');
            $totalLiabilities = $balanceSheet['liabilities']->sum('balance');
            $totalEquity = $balanceSheet['equity']->sum('balance');
            
            $currency = Currency::where('code', $currencyCode)->first();
            $baseCurrency = Currency::getBaseCurrency();

            return view('reports.balance-sheet', compact(
                'balanceSheet',
                'totalAssets',
                'totalLiabilities', 
                'totalEquity',
                'asOfDate',
                'currency',
                'baseCurrency'
            ));

        } catch (\Exception $e) {
            return back()->withError('Error generating balance sheet: ' . $e->getMessage());
        }
    }

    /**
     * Cash Flow Statement
     */
    public function cashFlowStatement(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'currency' => 'nullable|string|size:3',
        ]);

        $startDate = $request->start_date;
        $endDate = $request->end_date;
        $currencyCode = $request->currency ?: 'USD';
        
        try {
            $cashFlow = CashFlowTransaction::getCashFlowStatement($startDate, $endDate, $currencyCode);
            
            $currency = Currency::where('code', $currencyCode)->first();
            $baseCurrency = Currency::getBaseCurrency();

            return view('reports.cash-flow', compact(
                'cashFlow',
                'startDate',
                'endDate', 
                'currency',
                'baseCurrency'
            ));

        } catch (\Exception $e) {
            return back()->withError('Error generating cash flow statement: ' . $e->getMessage());
        }
    }

    /**
     * Trial Balance
     */
    public function trialBalance(Request $request)
    {
        $request->validate([
            'as_of_date' => 'required|date',
            'currency' => 'nullable|string|size:3',
        ]);

        $asOfDate = $request->as_of_date;
        $currencyCode = $request->currency ?: 'USD';
        
        try {
            $trialBalance = AccountBalance::getTrialBalance($asOfDate, $currencyCode);
            
            $totalDebits = $trialBalance->sum('base_currency_debit');
            $totalCredits = $trialBalance->sum('base_currency_credit');
            
            $currency = Currency::where('code', $currencyCode)->first();
            $baseCurrency = Currency::getBaseCurrency();

            return view('reports.trial-balance', compact(
                'trialBalance',
                'totalDebits',
                'totalCredits',
                'asOfDate',
                'currency',
                'baseCurrency'
            ));

        } catch (\Exception $e) {
            return back()->withError('Error generating trial balance: ' . $e->getMessage());
        }
    }

    /**
     * Accounts Receivable Aging Report
     */
    public function agingReceivables(Request $request)
    {
        $request->validate([
            'as_of_date' => 'required|date',
        ]);

        $asOfDate = $request->as_of_date;
        
        try {
            $agingData = CashFlowTransaction::getAgedReceivables($asOfDate);
            
            $totals = [
                'current' => $agingData['current']->sum('amount'),
                '1_30' => $agingData['1_30']->sum('amount'),
                '31_60' => $agingData['31_60']->sum('amount'),
                '61_90' => $agingData['61_90']->sum('amount'),
                'over_90' => $agingData['over_90']->sum('amount'),
            ];

            $currency = Currency::getBaseCurrency();

            return view('reports.aging-receivables', compact(
                'agingData',
                'totals',
                'asOfDate',
                'currency'
            ));

        } catch (\Exception $e) {
            return back()->withError('Error generating aging receivables report: ' . $e->getMessage());
        }
    }

    /**
     * Accounts Payable Aging Report
     */
    public function agingPayables(Request $request)
    {
        $request->validate([
            'as_of_date' => 'required|date',
        ]);

        $asOfDate = $request->as_of_date;
        
        try {
            $agingData = CashFlowTransaction::getAgedPayables($asOfDate);
            
            $totals = [
                'current' => $agingData['current']->sum('amount'),
                '1_30' => $agingData['1_30']->sum('amount'),
                '31_60' => $agingData['31_60']->sum('amount'),
                '61_90' => $agingData['61_90']->sum('amount'),
                'over_90' => $agingData['over_90']->sum('amount'),
            ];

            $currency = Currency::getBaseCurrency();

            return view('reports.aging-payables', compact(
                'agingData',
                'totals',
                'asOfDate',
                'currency'
            ));

        } catch (\Exception $e) {
            return back()->withError('Error generating aging payables report: ' . $e->getMessage());
        }
    }

    /**
     * Tax Report
     */
    public function taxReport(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $startDate = $request->start_date;
        $endDate = $request->end_date;
        
        try {
            // Calculate sales tax collected
            $invoicesWithTax = Invoice::whereBetween('issue_date', [$startDate, $endDate])
                ->where('tax_amount', '>', 0)
                ->with('customer')
                ->get();

            $salesTaxCollected = $invoicesWithTax->sum('tax_amount');

            // Calculate expense tax deductions
            $taxDeductibleExpenses = Expense::whereBetween('expense_date', [$startDate, $endDate])
                ->where('is_tax_deductible', true)
                ->with('category')
                ->get();

            $taxDeductibleAmount = $taxDeductibleExpenses->sum('amount');

            // Group tax by category
            $taxByCategory = $taxDeductibleExpenses->groupBy('category.name')->map(function ($expenses) {
                return $expenses->sum('amount');
            });

            $currency = Currency::getBaseCurrency();

            return view('reports.tax-report', compact(
                'startDate',
                'endDate',
                'invoicesWithTax',
                'salesTaxCollected',
                'taxDeductibleExpenses',
                'taxDeductibleAmount',
                'taxByCategory',
                'currency'
            ));

        } catch (\Exception $e) {
            return back()->withError('Error generating tax report: ' . $e->getMessage());
        }
    }

    /**
     * Profit & Loss Statement (Enhanced)
     */
    public function profitLoss(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'currency' => 'nullable|string|size:3',
        ]);

        $startDate = $request->start_date;
        $endDate = $request->end_date;
        $currencyCode = $request->currency ?: 'USD';
        
        try {
            // Get revenue from invoices
            $revenue = Invoice::whereBetween('issue_date', [$startDate, $endDate])
                ->sum('total_amount');

            // Get expenses
            $expenses = Expense::whereBetween('expense_date', [$startDate, $endDate])
                ->sum('amount');

            // Calculate gross profit
            $grossProfit = $revenue - $expenses;

            // Group expenses by category for detailed view
            $expensesByCategory = Expense::whereBetween('expense_date', [$startDate, $endDate])
                ->with('category')
                ->get()
                ->groupBy('category.name')
                ->map(function ($expenses) {
                    return $expenses->sum('amount');
                });

            $currency = Currency::where('code', $currencyCode)->first();
            $baseCurrency = Currency::getBaseCurrency();

            return view('reports.profit-loss', compact(
                'startDate',
                'endDate',
                'revenue',
                'expenses',
                'grossProfit',
                'expensesByCategory',
                'currency',
                'baseCurrency'
            ));

        } catch (\Exception $e) {
            return back()->withError('Error generating profit & loss statement: ' . $e->getMessage());
        }
    }

    /**
     * Export report to PDF
     */
    public function exportPdf(Request $request, $reportType)
    {
        // This would implement PDF export functionality
        // For now, return a simple message
        return back()->with('info', 'PDF export functionality will be implemented soon.');
    }

    /**
     * Schedule automatic report generation
     */
    public function scheduleReport(Request $request)
    {
        $request->validate([
            'report_type' => 'required|in:balance_sheet,cash_flow,trial_balance,aging_receivables,aging_payables,tax_report',
            'frequency' => 'required|in:daily,weekly,monthly,quarterly',
            'email' => 'required|email',
        ]);

        // This would implement report scheduling
        // For now, return success message
        return back()->with('success', 'Report scheduling will be implemented soon.');
    }
}