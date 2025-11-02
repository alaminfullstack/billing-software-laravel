<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Expense;
use App\Models\Payment;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ReportsController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;

        // Monthly Summary
        $monthlyStats = [
            'revenue' => Invoice::where('created_by', $user->id)
                              ->whereMonth('issue_date', $currentMonth)
                              ->whereYear('issue_date', $currentYear)
                              ->where('status', 'paid')
                              ->sum('total_amount'),
            'expenses' => Expense::where('created_by', $user->id)
                               ->whereMonth('expense_date', $currentMonth)
                               ->whereYear('expense_date', $currentYear)
                               ->sum('amount'),
            'invoices_count' => Invoice::where('created_by', $user->id)
                                     ->whereMonth('issue_date', $currentMonth)
                                     ->whereYear('issue_date', $currentYear)
                                     ->count(),
            'customers_count' => Customer::where('user_id', $user->id)->count(),
        ];

        $monthlyStats['profit'] = $monthlyStats['revenue'] - $monthlyStats['expenses'];

        // Year to Date Summary
        $yearlyStats = [
            'revenue' => Invoice::where('created_by', $user->id)
                              ->whereYear('issue_date', $currentYear)
                              ->where('status', 'paid')
                              ->sum('total_amount'),
            'expenses' => Expense::where('created_by', $user->id)
                               ->whereYear('expense_date', $currentYear)
                               ->sum('amount'),
            'invoices_count' => Invoice::where('created_by', $user->id)
                                     ->whereYear('issue_date', $currentYear)
                                     ->count(),
        ];

        $yearlyStats['profit'] = $yearlyStats['revenue'] - $yearlyStats['expenses'];

        return view('reports.index', compact('monthlyStats', 'yearlyStats'));
    }

    public function revenueReport(Request $request)
    {
        $user = Auth::user();
        
        $startDate = $request->start_date ?? Carbon::now()->startOfMonth()->toDateString();
        $endDate = $request->end_date ?? Carbon::now()->endOfMonth()->toDateString();

        $revenueData = Invoice::where('created_by', $user->id)
                             ->whereBetween('issue_date', [$startDate, $endDate])
                             ->where('status', 'paid')
                             ->selectRaw('
                                 DATE(issue_date) as date,
                                 SUM(total_amount) as total,
                                 COUNT(*) as count
                             ')
                             ->groupBy('issue_date')
                             ->orderBy('issue_date')
                             ->get();

        $totalRevenue = $revenueData->sum('total');
        $totalInvoices = $revenueData->sum('count');

        return view('reports.revenue', compact('revenueData', 'totalRevenue', 'totalInvoices', 'startDate', 'endDate'));
    }

    public function expenseReport(Request $request)
    {
        $user = Auth::user();
        
        $startDate = $request->start_date ?? Carbon::now()->startOfMonth()->toDateString();
        $endDate = $request->end_date ?? Carbon::now()->endOfMonth()->toDateString();

        $expenseData = Expense::where('created_by', $user->id)
                             ->whereBetween('expense_date', [$startDate, $endDate])
                             ->with('category')
                             ->selectRaw('
                                 DATE(expense_date) as date,
                                 SUM(amount) as total,
                                 COUNT(*) as count
                             ')
                             ->groupBy('expense_date')
                             ->orderBy('expense_date')
                             ->get();

        $totalExpenses = $expenseData->sum('total');

        // Category-wise expenses
        $categoryExpenses = Expense::where('created_by', $user->id)
                                  ->whereBetween('expense_date', [$startDate, $endDate])
                                  ->with('category')
                                  ->selectRaw('
                                      expense_category_id,
                                      SUM(amount) as total,
                                      COUNT(*) as count
                                  ')
                                  ->groupBy('expense_category_id')
                                  ->get();

        return view('reports.expenses', compact('expenseData', 'totalExpenses', 'categoryExpenses', 'startDate', 'endDate'));
    }

    public function profitLossReport(Request $request)
    {
        $user = Auth::user();
        
        $startDate = $request->start_date ?? Carbon::now()->startOfMonth()->toDateString();
        $endDate = $request->end_date ?? Carbon::now()->endOfMonth()->toDateString();

        // Revenue data
        $revenue = Invoice::where('created_by', $user->id)
                         ->whereBetween('issue_date', [$startDate, $endDate])
                         ->where('status', 'paid')
                         ->sum('total_amount');

        // Expense data
        $expenses = Expense::where('created_by', $user->id)
                          ->whereBetween('expense_date', [$startDate, $endDate])
                          ->sum('amount');

        $profit = $revenue - $expenses;

        // Monthly breakdown for chart
        $monthlyBreakdown = [];
        $currentDate = Carbon::parse($startDate);
        $endCarbonDate = Carbon::parse($endDate);

        while ($currentDate <= $endCarbonDate) {
            $monthStart = $currentDate->copy()->startOfMonth();
            $monthEnd = $currentDate->copy()->endOfMonth();
            
            $monthRevenue = Invoice::where('created_by', $user->id)
                                  ->whereBetween('issue_date', [$monthStart, $monthEnd])
                                  ->where('status', 'paid')
                                  ->sum('total_amount');
            
            $monthExpense = Expense::where('created_by', $user->id)
                                  ->whereBetween('expense_date', [$monthStart, $monthEnd])
                                  ->sum('amount');
            
            $monthlyBreakdown[] = [
                'month' => $currentDate->format('M Y'),
                'revenue' => $monthRevenue,
                'expenses' => $monthExpense,
                'profit' => $monthRevenue - $monthExpense,
            ];
            
            $currentDate->addMonth();
        }

        return view('reports.profit-loss', compact('revenue', 'expenses', 'profit', 'monthlyBreakdown', 'startDate', 'endDate'));
    }

    public function customerReport(Request $request)
    {
        $user = Auth::user();

        $customers = Customer::where('user_id', $user->id)
                           ->with(['invoices' => function ($query) {
                               $query->where('status', 'paid');
                           }])
                           ->get()
                           ->map(function ($customer) {
                               $totalRevenue = $customer->invoices->sum('total_amount');
                               $invoiceCount = $customer->invoices->count();
                               $avgInvoiceValue = $invoiceCount > 0 ? $totalRevenue / $invoiceCount : 0;
                               
                               return [
                                   'customer' => $customer,
                                   'total_revenue' => $totalRevenue,
                                   'invoice_count' => $invoiceCount,
                                   'avg_invoice_value' => $avgInvoiceValue,
                               ];
                           })
                           ->sortByDesc('total_revenue')
                           ->values();

        return view('reports.customers', compact('customers'));
    }

    public function invoiceStatusReport()
    {
        $user = Auth::user();

        $statusData = Invoice::where('created_by', $user->id)
                           ->selectRaw('
                               status,
                               COUNT(*) as count,
                               SUM(total_amount) as total_amount,
                               SUM(paid_amount) as paid_amount,
                               SUM(balance_amount) as balance_amount
                           ')
                           ->groupBy('status')
                           ->get();

        return view('reports.invoice-status', compact('statusData'));
    }
}
