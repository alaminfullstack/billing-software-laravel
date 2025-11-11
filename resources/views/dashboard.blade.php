@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Dashboard</h1>
            <p class="text-muted">Welcome back! Here's what's happening with your business.</p>
        </div>
        <div class="d-flex align-items-center">
            <span class="text-muted me-3">Last updated: {{ now()->format('M d, Y H:i') }}</span>
            <button class="btn btn-outline-primary btn-sm" onclick="refreshDashboard()">
                <i class="fas fa-sync-alt"></i> Refresh
            </button>
        </div>
    </div>

    <!-- Key Metrics Row -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Monthly Revenue
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                ${{ number_format($stats['monthly_revenue'] ?? 0, 2) }}
                            </div>
                            <div class="text-xs text-muted">
                                <i class="fas fa-arrow-{{ ($revenueGrowth ?? 0) >= 0 ? 'up text-success' : 'down text-danger' }}"></i>
                                {{ abs($revenueGrowth ?? 0) }}% from last month
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Monthly Profit
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                ${{ number_format($stats['monthly_profit'] ?? 0, 2) }}
                            </div>
                            <div class="text-xs text-muted">
                                {{ number_format($profitMargin ?? 0, 1) }}% profit margin
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-chart-line fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Outstanding Invoices
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                ${{ number_format($outstandingAmount ?? 0, 2) }}
                            </div>
                            <div class="text-xs text-muted">
                                {{ $overdueInvoices ?? 0 }} overdue invoices
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Active Customers
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $stats['active_customers'] ?? 0 }}
                            </div>
                            <div class="text-xs text-muted">
                                {{ $newCustomersThisMonth ?? 0 }} new this month
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row mb-4">
        <!-- Revenue Trend Chart -->
        <div class="col-xl-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-chart-area"></i> Revenue & Expenses Trend
                    </h6>
                    <div class="dropdown no-arrow">
                        <a class="dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in">
                            <div class="dropdown-header">Chart Options:</div>
                            <a class="dropdown-item" href="#" onclick="changeChartType('line')">Line Chart</a>
                            <a class="dropdown-item" href="#" onclick="changeChartType('bar')">Bar Chart</a>
                            <a class="dropdown-item" href="#" onclick="exportChart()">Export Chart</a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="revenueChart" width="400" height="200"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="col-xl-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-bolt"></i> Quick Actions
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('invoices.create') }}" class="btn btn-primary">
                            <i class="fas fa-file-invoice"></i> Create Invoice
                        </a>
                        <a href="{{ route('customers.create') }}" class="btn btn-success">
                            <i class="fas fa-user-plus"></i> Add Customer
                        </a>
                        <a href="{{ route('payments.create') }}" class="btn btn-info">
                            <i class="fas fa-money-bill"></i> Record Payment
                        </a>
                        <a href="{{ route('expenses.create') }}" class="btn btn-warning">
                            <i class="fas fa-receipt"></i> Add Expense
                        </a>
                        <a href="{{ route('products.create') }}" class="btn btn-secondary">
                            <i class="fas fa-box"></i> Add Product/Service
                        </a>
                        <a href="{{ route('reports.index') }}" class="btn btn-dark">
                            <i class="fas fa-chart-bar"></i> View Reports
                        </a>
                    </div>
                </div>
            </div>

            <!-- Invoice Status Pie Chart -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-chart-pie"></i> Invoice Status
                    </h6>
                </div>
                <div class="card-body">
                    <div class="chart-pie">
                        <canvas id="invoiceStatusChart" width="300" height="200"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity & Alerts Row -->
    <div class="row">
        <!-- Recent Invoices -->
        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-file-invoice"></i> Recent Invoices
                        <span class="badge bg-primary float-end">{{ $recentInvoices->count() ?? 0 }}</span>
                    </h6>
                </div>
                <div class="card-body">
                    @if(($recentInvoices ?? collect())->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-borderless">
                                <tbody>
                                    @foreach($recentInvoices as $invoice)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-sm bg-primary text-white rounded d-flex align-items-center justify-content-center me-3" style="width: 35px; height: 35px;">
                                                    <i class="fas fa-file-invoice"></i>
                                                </div>
                                                <div>
                                                    <strong>#{{ $invoice->invoice_number }}</strong><br>
                                                    <small class="text-muted">{{ $invoice->customer->name ?? 'No Customer' }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-end">
                                            <span class="badge bg-{{ $invoice->status == 'paid' ? 'success' : ($invoice->status == 'overdue' ? 'danger' : ($invoice->status == 'sent' ? 'info' : 'secondary')) }}">
                                                {{ ucfirst($invoice->status) }}
                                            </span>
                                            <div class="fw-bold">${{ number_format($invoice->total, 2) }}</div>
                                            <small class="text-muted">{{ \Carbon\Carbon::parse($invoice->invoice_date)->format('M d, Y') }}</small>
                                        </td>
                                        <td class="text-center">
                                            <a href="{{ route('invoices.show', $invoice) }}" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    @if(!$loop->last)<tr><td colspan="3"><hr class="my-2"></td></tr>@endif
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="text-center">
                            <a href="{{ route('invoices.index') }}" class="btn btn-sm btn-outline-primary">
                                View All Invoices <i class="fas fa-arrow-right"></i>
                            </a>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-file-invoice fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No recent invoices</p>
                            <a href="{{ route('invoices.create') }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-plus"></i> Create First Invoice
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Recent Payments -->
        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-money-bill"></i> Recent Payments
                        <span class="badge bg-success float-end">{{ $recentPayments->count() ?? 0 }}</span>
                    </h6>
                </div>
                <div class="card-body">
                    @if(($recentPayments ?? collect())->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-borderless">
                                <tbody>
                                    @foreach($recentPayments as $payment)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-sm bg-success text-white rounded d-flex align-items-center justify-content-center me-3" style="width: 35px; height: 35px;">
                                                    <i class="fas fa-money-bill"></i>
                                                </div>
                                                <div>
                                                    <strong>{{ $payment->invoice->invoice_number ?? 'N/A' }}</strong><br>
                                                    <small class="text-muted">{{ $payment->customer->name ?? $payment->invoice->customer->name ?? 'Unknown' }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-end">
                                            <div class="fw-bold text-success">${{ number_format($payment->amount, 2) }}</div>
                                            <small class="text-muted">{{ ucfirst($payment->payment_method) }}</small><br>
                                            <small class="text-muted">{{ $payment->payment_date->format('M d, Y') }}</small>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-{{ $payment->status == 'completed' ? 'success' : ($payment->status == 'pending' ? 'warning' : 'danger') }}">
                                                {{ ucfirst($payment->status) }}
                                            </span>
                                        </td>
                                    </tr>
                                    @if(!$loop->last)<tr><td colspan="3"><hr class="my-2"></td></tr>@endif
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="text-center">
                            <a href="{{ route('payments.index') }}" class="btn btn-sm btn-outline-primary">
                                View All Payments <i class="fas fa-arrow-right"></i>
                            </a>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-money-bill fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No recent payments</p>
                            <a href="{{ route('payments.create') }}" class="btn btn-success btn-sm">
                                <i class="fas fa-plus"></i> Record First Payment
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Alerts & Notifications -->
    @if(($alerts ?? collect())->count() > 0)
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-bell"></i> Alerts & Notifications
                    </h6>
                </div>
                <div class="card-body">
                    @foreach($alerts as $alert)
                    <div class="alert alert-{{ $alert['type'] }} alert-dismissible fade show" role="alert">
                        <i class="fas {{ $alert['icon'] }} me-2"></i>
                        {{ $alert['message'] }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Performance Summary -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-chart-bar"></i> Monthly Performance Summary
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-md-2">
                            <div class="border rounded p-3">
                                <h4 class="text-primary">{{ $stats['total_invoices'] ?? 0 }}</h4>
                                <small class="text-muted">Total Invoices</small>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="border rounded p-3">
                                <h4 class="text-success">{{ $stats['paid_invoices'] ?? 0 }}</h4>
                                <small class="text-muted">Paid Invoices</small>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="border rounded p-3">
                                <h4 class="text-warning">{{ $stats['pending_invoices'] ?? 0 }}</h4>
                                <small class="text-muted">Pending Invoices</small>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="border rounded p-3">
                                <h4 class="text-info">{{ $stats['total_customers'] ?? 0 }}</h4>
                                <small class="text-muted">Total Customers</small>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="border rounded p-3">
                                <h4 class="text-secondary">{{ $stats['total_products'] ?? 0 }}</h4>
                                <small class="text-muted">Products/Services</small>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="border rounded p-3">
                                <h4 class="text-dark">{{ $stats['total_expenses'] ?? 0 }}</h4>
                                <small class="text-muted">Total Expenses</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.avatar-sm {
    font-size: 0.75rem;
}

.card {
    border: none;
    box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15) !important;
}

.border-left-primary {
    border-left: 0.25rem solid #4e73df !important;
}

.border-left-success {
    border-left: 0.25rem solid #1cc88a !important;
}

.border-left-warning {
    border-left: 0.25rem solid #f6c23e !important;
}

.border-left-info {
    border-left: 0.25rem solid #36b9cc !important;
}

.text-xs {
    font-size: 0.7rem;
}

.chart-area {
    position: relative;
    height: 20rem;
    width: 100%;
}

.chart-pie {
    position: relative;
    height: 15rem;
    width: 100%;
}
</style>

@section('scripts')
<script>
let revenueChart, invoiceStatusChart;

// Initialize charts when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    initializeRevenueChart();
    initializeInvoiceStatusChart();
});

function initializeRevenueChart() {
    const ctx = document.getElementById('revenueChart');
    if (ctx) {
        revenueChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: @json($months ?? []),
                datasets: [{
                    label: 'Revenue',
                    data: @json($monthlyRevenue ?? []),
                    borderColor: '#4e73df',
                    backgroundColor: 'rgba(78, 115, 223, 0.1)',
                    tension: 0.1,
                    fill: true
                }, {
                    label: 'Expenses',
                    data: @json($monthlyExpenses ?? []),
                    borderColor: '#e74a3b',
                    backgroundColor: 'rgba(231, 74, 59, 0.1)',
                    tension: 0.1,
                    fill: true
                }, {
                    label: 'Profit',
                    data: @json($monthlyProfit ?? []),
                    borderColor: '#1cc88a',
                    backgroundColor: 'rgba(28, 200, 138, 0.1)',
                    tension: 0.1,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top'
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false,
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                label += '$' + context.parsed.y.toLocaleString();
                                return label;
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: {
                            display: false
                        }
                    },
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return '$' + value.toLocaleString();
                            }
                        }
                    }
                },
                interaction: {
                    mode: 'nearest',
                    axis: 'x',
                    intersect: false
                }
            }
        });
    }
}

function initializeInvoiceStatusChart() {
    const ctx = document.getElementById('invoiceStatusChart');
    if (ctx) {
        invoiceStatusChart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Paid', 'Sent', 'Draft', 'Overdue'],
                datasets: [{
                    data: [
                        {{ $stats['paid_invoices'] ?? 0 }},
                        {{ $stats['sent_invoices'] ?? 0 }},
                        {{ $stats['draft_invoices'] ?? 0 }},
                        {{ $stats['overdue_invoices'] ?? 0 }}
                    ],
                    backgroundColor: [
                        '#1cc88a',
                        '#36b9cc',
                        '#858796',
                        '#e74a3b'
                    ],
                    borderWidth: 2,
                    borderColor: '#ffffff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 20,
                            usePointStyle: true
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const label = context.label || '';
                                const value = context.parsed;
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
                                return label + ': ' + value + ' (' + percentage + '%)';
                            }
                        }
                    }
                }
            }
        });
    }
}

function changeChartType(type) {
    if (revenueChart) {
        revenueChart.config.type = type;
        revenueChart.update();
    }
}

function exportChart() {
    if (revenueChart) {
        const url = revenueChart.toBase64Image();
        const link = document.createElement('a');
        link.download = 'revenue-chart.png';
        link.href = url;
        link.click();
    }
}

function refreshDashboard() {
    window.location.reload();
}

// Auto-refresh every 5 minutes
setTimeout(function() {
    if (document.visibilityState === 'visible') {
        refreshDashboard();
    }
}, 300000); // 5 minutes
</script>
@endsection
@endsection