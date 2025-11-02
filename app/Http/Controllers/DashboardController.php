<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Expense;
use App\Models\Payment;
use App\Models\FinancialSummary;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;

        // Get dashboard statistics
        $stats = [
            'total_customers' => Customer::where('user_id', $user->id)->count(),
            'total_invoices' => Invoice::where('created_by', $user->id)->count(),
            'pending_invoices' => Invoice::where('created_by', $user->id)
                ->whereIn('status', ['sent', 'overdue'])
                ->count(),
            'monthly_revenue' => Invoice::where('created_by', $user->id)
                ->whereMonth('issue_date', $currentMonth)
                ->whereYear('issue_date', $currentYear)
                ->where('status', 'paid')
                ->sum('total_amount'),
            'monthly_expenses' => Expense::where('created_by', $user->id)
                ->whereMonth('expense_date', $currentMonth)
                ->whereYear('expense_date', $currentYear)
                ->sum('amount'),
        ];

        $stats['monthly_profit'] = $stats['monthly_revenue'] - $stats['monthly_expenses'];

        // Recent invoices
        $recentInvoices = Invoice::where('created_by', $user->id)
            ->with('customer')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Recent payments
        $recentPayments = Payment::where('created_by', $user->id)
            ->with('invoice.customer')
            ->orderBy('payment_date', 'desc')
            ->limit(5)
            ->get();

        // Recent expenses
        $recentExpenses = Expense::where('created_by', $user->id)
            ->with('category')
            ->orderBy('expense_date', 'desc')
            ->limit(5)
            ->get();

        // Monthly revenue chart data (last 12 months)
        $monthlyRevenue = [];
        $monthlyExpenses = [];
        $months = [];

        for ($i = 11; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $revenue = Invoice::where('created_by', $user->id)
                ->whereMonth('issue_date', $month->month)
                ->whereYear('issue_date', $month->year)
                ->where('status', 'paid')
                ->sum('total_amount');
            
            $expense = Expense::where('created_by', $user->id)
                ->whereMonth('expense_date', $month->month)
                ->whereYear('expense_date', $month->year)
                ->sum('amount');

            $monthlyRevenue[] = $revenue;
            $monthlyExpenses[] = $expense;
            $months[] = $month->format('M Y');
        }

        return view('dashboard', compact(
            'stats',
            'recentInvoices',
            'recentPayments',
            'recentExpenses',
            'monthlyRevenue',
            'monthlyExpenses',
            'months'
        ));
    }
}
