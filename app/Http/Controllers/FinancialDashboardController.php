<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Expense;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class FinancialDashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;
        $lastMonth = Carbon::now()->subMonth()->month;
        $lastMonthYear = Carbon::now()->subMonth()->year;

        // Key Performance Indicators
        $kpis = $this->calculateKPIs($user, $currentMonth, $currentYear, $lastMonth, $lastMonthYear);

        // Monthly Trends (last 12 months)
        $monthlyTrends = $this->getMonthlyTrends($user);

        // Top performing metrics
        $topMetrics = $this->getTopMetrics($user);

        // Financial health indicators
        $healthMetrics = $this->getFinancialHealth($user);

        // Cash flow analysis
        $cashFlow = $this->getCashFlowAnalysis($user);

        // Alert indicators
        $alerts = $this->getAlerts($user);

        return view('financial-dashboard', compact(
            'kpis',
            'monthlyTrends',
            'topMetrics',
            'healthMetrics',
            'cashFlow',
            'alerts'
        ));
    }

    private function calculateKPIs($user, $currentMonth, $currentYear, $lastMonth, $lastMonthYear)
    {
        // Current month metrics
        $currentRevenue = Invoice::where('created_by', $user->id)
            ->whereMonth('issue_date', $currentMonth)
            ->whereYear('issue_date', $currentYear)
            ->where('status', 'paid')
            ->sum('total_amount');

        $currentExpenses = Expense::where('created_by', $user->id)
            ->whereMonth('expense_date', $currentMonth)
            ->whereYear('expense_date', $currentYear)
            ->sum('amount');

        $currentProfit = $currentRevenue - $currentExpenses;
        $currentProfitMargin = $currentRevenue > 0 ? ($currentProfit / $currentRevenue) * 100 : 0;

        // Last month metrics for comparison
        $lastRevenue = Invoice::where('created_by', $user->id)
            ->whereMonth('issue_date', $lastMonth)
            ->whereYear('issue_date', $lastMonthYear)
            ->where('status', 'paid')
            ->sum('total_amount');

        $lastExpenses = Expense::where('created_by', $user->id)
            ->whereMonth('expense_date', $lastMonth)
            ->whereYear('expense_date', $lastMonthYear)
            ->sum('amount');

        $lastProfit = $lastRevenue - $lastExpenses;

        // Calculate percentage changes
        $revenueChange = $lastRevenue > 0 ? (($currentRevenue - $lastRevenue) / $lastRevenue) * 100 : 0;
        $expenseChange = $lastExpenses > 0 ? (($currentExpenses - $lastExpenses) / $lastExpenses) * 100 : 0;
        $profitChange = $lastProfit > 0 ? (($currentProfit - $lastProfit) / $lastProfit) * 100 : 0;

        // Outstanding amounts
        $outstandingInvoices = Invoice::where('created_by', $user->id)
            ->whereIn('status', ['sent', 'overdue'])
            ->sum('balance_amount');

        $overdueInvoices = Invoice::where('created_by', $user->id)
            ->where('status', 'overdue')
            ->sum('balance_amount');

        // Customer metrics
        $totalCustomers = Customer::where('user_id', $user->id)->count();
        $activeCustomers = Customer::where('user_id', $user->id)
            ->whereHas('invoices', function ($query) use ($currentMonth, $currentYear) {
                $query->whereMonth('issue_date', $currentMonth)
                      ->whereYear('issue_date', $currentYear);
            })->count();

        // Average metrics
        $avgInvoiceValue = Invoice::where('created_by', $user->id)
            ->whereMonth('issue_date', $currentMonth)
            ->whereYear('issue_date', $currentYear)
            ->where('status', 'paid')
            ->avg('total_amount') ?? 0;

        $avgPaymentTime = Payment::where('created_by', $user->id)
            ->whereMonth('payment_date', $currentMonth)
            ->whereYear('payment_date', $currentYear)
            ->with('invoice')
            ->get()
            ->map(function ($payment) {
                return $payment->created_at->diffInDays($payment->invoice->issue_date);
            })
            ->avg() ?? 0;

        return [
            'revenue' => [
                'current' => $currentRevenue,
                'last_month' => $lastRevenue,
                'change' => $revenueChange,
                'formatted' => '$' . number_format($currentRevenue, 2),
            ],
            'expenses' => [
                'current' => $currentExpenses,
                'last_month' => $lastExpenses,
                'change' => $expenseChange,
                'formatted' => '$' . number_format($currentExpenses, 2),
            ],
            'profit' => [
                'current' => $currentProfit,
                'last_month' => $lastProfit,
                'change' => $profitChange,
                'formatted' => '$' . number_format($currentProfit, 2),
            ],
            'profit_margin' => [
                'current' => $currentProfitMargin,
                'formatted' => number_format($currentProfitMargin, 1) . '%',
            ],
            'outstanding' => [
                'total' => $outstandingInvoices,
                'overdue' => $overdueInvoices,
                'formatted_total' => '$' . number_format($outstandingInvoices, 2),
                'formatted_overdue' => '$' . number_format($overdueInvoices, 2),
            ],
            'customers' => [
                'total' => $totalCustomers,
                'active' => $activeCustomers,
                'rate' => $totalCustomers > 0 ? ($activeCustomers / $totalCustomers) * 100 : 0,
            ],
            'avg_invoice' => [
                'value' => $avgInvoiceValue,
                'formatted' => '$' . number_format($avgInvoiceValue, 2),
            ],
            'payment_time' => [
                'days' => $avgPaymentTime,
                'formatted' => number_format($avgPaymentTime, 1) . ' days',
            ],
        ];
    }

    private function getMonthlyTrends($user)
    {
        $trends = [];
        $months = [];

        for ($i = 11; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $months[] = $month->format('M Y');

            $revenue = Invoice::where('created_by', $user->id)
                ->whereMonth('issue_date', $month->month)
                ->whereYear('issue_date', $month->year)
                ->where('status', 'paid')
                ->sum('total_amount');
            
            $expenses = Expense::where('created_by', $user->id)
                ->whereMonth('expense_date', $month->month)
                ->whereYear('expense_date', $month->year)
                ->sum('amount');

            $trends[] = [
                'month' => $month->format('M Y'),
                'revenue' => $revenue,
                'expenses' => $expenses,
                'profit' => $revenue - $expenses,
            ];
        }

        return [
            'months' => $months,
            'data' => $trends,
        ];
    }

    private function getTopMetrics($user)
    {
        // Top customers by revenue
        $topCustomers = Customer::where('user_id', $user->id)
            ->with(['invoices' => function ($query) {
                $query->where('status', 'paid');
            }])
            ->get()
            ->map(function ($customer) {
                $totalRevenue = $customer->invoices->sum('total_amount');
                $invoiceCount = $customer->invoices->count();
                
                return [
                    'customer' => $customer,
                    'revenue' => $totalRevenue,
                    'invoices' => $invoiceCount,
                ];
            })
            ->sortByDesc('revenue')
            ->take(5)
            ->values();

        // Top products/services
        $topProducts = Product::with(['invoiceItems' => function ($query) {
                $query->whereHas('invoice', function ($q) {
                    $q->where('status', 'paid');
                });
            }])
            ->get()
            ->map(function ($product) {
                $quantity = $product->invoiceItems->sum('quantity');
                $revenue = $product->invoiceItems->sum('total_amount');
                
                return [
                    'product' => $product,
                    'quantity' => $quantity,
                    'revenue' => $revenue,
                ];
            })
            ->sortByDesc('revenue')
            ->take(5)
            ->values();

        return [
            'customers' => $topCustomers,
            'products' => $topProducts,
        ];
    }

    private function getFinancialHealth($user)
    {
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;

        $revenue = Invoice::where('created_by', $user->id)
            ->whereMonth('issue_date', $currentMonth)
            ->whereYear('issue_date', $currentYear)
            ->where('status', 'paid')
            ->sum('total_amount');

        $expenses = Expense::where('created_by', $user->id)
            ->whereMonth('expense_date', $currentMonth)
            ->whereYear('expense_date', $currentYear)
            ->sum('amount');

        $profit = $revenue - $expenses;

        // Financial ratios
        $grossMargin = $revenue > 0 ? ($profit / $revenue) * 100 : 0;
        $expenseRatio = $revenue > 0 ? ($expenses / $revenue) * 100 : 0;
        $breakEvenPoint = $expenses;
        $runwayMonths = $profit > 0 ? 12 : 0; // Simplified calculation

        // Collection efficiency
        $totalInvoices = Invoice::where('created_by', $user->id)
            ->whereMonth('issue_date', $currentMonth)
            ->whereYear('issue_date', $currentYear)
            ->count();

        $paidInvoices = Invoice::where('created_by', $user->id)
            ->whereMonth('issue_date', $currentMonth)
            ->whereYear('issue_date', $currentYear)
            ->where('status', 'paid')
            ->count();

        $collectionEfficiency = $totalInvoices > 0 ? ($paidInvoices / $totalInvoices) * 100 : 0;

        return [
            'gross_margin' => [
                'value' => $grossMargin,
                'formatted' => number_format($grossMargin, 1) . '%',
            ],
            'expense_ratio' => [
                'value' => $expenseRatio,
                'formatted' => number_format($expenseRatio, 1) . '%',
            ],
            'break_even' => [
                'amount' => $breakEvenPoint,
                'formatted' => '$' . number_format($breakEvenPoint, 2),
            ],
            'collection_efficiency' => [
                'value' => $collectionEfficiency,
                'formatted' => number_format($collectionEfficiency, 1) . '%',
            ],
            'health_score' => $this->calculateHealthScore($grossMargin, $expenseRatio, $collectionEfficiency),
        ];
    }

    private function getCashFlowAnalysis($user)
    {
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;
        $nextMonth = Carbon::now()->addMonth()->month;
        $nextMonthYear = Carbon::now()->addMonth()->year;

        // Expected inflows (due invoices)
        $expectedInflows = Invoice::where('created_by', $user->id)
            ->whereIn('status', ['sent', 'overdue'])
            ->where(function ($query) use ($currentMonth, $currentYear, $nextMonth, $nextMonthYear) {
                $query->where(function ($q) use ($currentMonth, $currentYear) {
                    $q->whereMonth('due_date', $currentMonth)
                      ->whereYear('due_date', $currentYear);
                })->orWhere(function ($q) use ($nextMonth, $nextMonthYear) {
                    $q->whereMonth('due_date', $nextMonth)
                      ->whereYear('due_date', $nextMonthYear);
                });
            })
            ->sum('balance_amount');

        // Expected outflows (upcoming expenses)
        $expectedOutflows = Expense::where('created_by', $user->id)
            ->whereMonth('expense_date', $nextMonth)
            ->whereYear('expense_date', $nextMonthYear)
            ->where('status', '!=', 'rejected')
            ->sum('amount');

        return [
            'expected_inflows' => [
                'amount' => $expectedInflows,
                'formatted' => '$' . number_format($expectedInflows, 2),
            ],
            'expected_outflows' => [
                'amount' => $expectedOutflows,
                'formatted' => '$' . number_format($expectedOutflows, 2),
            ],
            'net_flow' => [
                'amount' => $expectedInflows - $expectedOutflows,
                'formatted' => '$' . number_format($expectedInflows - $expectedOutflows, 2),
            ],
        ];
    }

    private function getAlerts($user)
    {
        $alerts = [];

        // Overdue invoices
        $overdueInvoices = Invoice::where('created_by', $user->id)
            ->where('status', 'overdue')
            ->count();

        if ($overdueInvoices > 0) {
            $alerts[] = [
                'type' => 'danger',
                'title' => 'Overdue Invoices',
                'message' => "You have {$overdueInvoices} overdue invoice(s) requiring attention.",
                'action' => ['route' => 'invoices.index', 'params' => ['status' => 'overdue']],
            ];
        }

        // Low stock alerts
        $lowStockProducts = Product::where('stock_quantity', '<=', 'low_stock_alert')
            ->where('track_inventory', true)
            ->count();

        if ($lowStockProducts > 0) {
            $alerts[] = [
                'type' => 'warning',
                'title' => 'Low Stock Alert',
                'message' => "{$lowStockProducts} product(s) are running low on stock.",
                'action' => ['route' => 'products.index', 'params' => ['low_stock' => true]],
            ];
        }

        // Pending expenses
        $pendingExpenses = Expense::where('created_by', $user->id)
            ->where('status', 'pending')
            ->count();

        if ($pendingExpenses > 0) {
            $alerts[] = [
                'type' => 'info',
                'title' => 'Pending Expenses',
                'message' => "{$pendingExpenses} expense(s) are waiting for approval.",
                'action' => ['route' => 'expenses.index', 'params' => ['status' => 'pending']],
            ];
        }

        return $alerts;
    }

    private function calculateHealthScore($grossMargin, $expenseRatio, $collectionEfficiency)
    {
        // Simple health score calculation (0-100)
        $marginScore = min(($grossMargin + 20) * 2, 40); // Cap at 40 points
        $expenseScore = max(40 - $expenseRatio, 0); // Inverted scale, cap at 40 points
        $collectionScore = min($collectionEfficiency * 0.2, 20); // Cap at 20 points

        return round($marginScore + $expenseScore + $collectionScore);
    }
}
