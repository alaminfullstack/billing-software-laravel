@extends('layouts.app')

@section('title', 'Financial Reports')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1><i class="fas fa-chart-line"></i> Financial Reports</h1>
                <div class="btn-group">
                    <button type="button" class="btn btn-outline-primary" onclick="window.print()">
                        <i class="fas fa-print"></i> Print
                    </button>
                    <button type="button" class="btn btn-outline-success" onclick="exportReport()">
                        <i class="fas fa-download"></i> Export
                    </button>
                </div>
            </div>

            <!-- Report Filters -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5><i class="fas fa-filter"></i> Report Filters</h5>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('reports.index') }}" class="row g-3">
                        <div class="col-md-3">
                            <label for="report_type" class="form-label">Report Type</label>
                            <select name="report_type" id="report_type" class="form-select" onchange="updateReportType()">
                                <option value="profit_loss" {{ request('report_type', 'profit_loss') == 'profit_loss' ? 'selected' : '' }}>Profit & Loss</option>
                                <option value="cash_flow" {{ request('report_type') == 'cash_flow' ? 'selected' : '' }}>Cash Flow</option>
                                <option value="balance_sheet" {{ request('report_type') == 'balance_sheet' ? 'selected' : '' }}>Balance Sheet</option>
                                <option value="customer_summary" {{ request('report_type') == 'customer_summary' ? 'selected' : '' }}>Customer Summary</option>
                                <option value="product_performance" {{ request('report_type') == 'product_performance' ? 'selected' : '' }}>Product Performance</option>
                                <option value="aging_report" {{ request('report_type') == 'aging_report' ? 'selected' : '' }}>Aging Report</option>
                                <option value="sales_tax" {{ request('report_type') == 'sales_tax' ? 'selected' : '' }}>Sales Tax Report</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="date_from" class="form-label">Date From</label>
                            <input type="date" name="date_from" id="date_from" class="form-control" 
                                   value="{{ request('date_from', date('Y-m-01')) }}">
                        </div>
                        <div class="col-md-3">
                            <label for="date_to" class="form-label">Date To</label>
                            <input type="date" name="date_to" id="date_to" class="form-control" 
                                   value="{{ request('date_to', date('Y-m-d')) }}">
                        </div>
                        <div class="col-md-3">
                            <label for="group_by" class="form-label">Group By</label>
                            <select name="group_by" id="group_by" class="form-select">
                                <option value="month" {{ request('group_by', 'month') == 'month' ? 'selected' : '' }}>Month</option>
                                <option value="quarter" {{ request('group_by') == 'quarter' ? 'selected' : '' }}>Quarter</option>
                                <option value="year" {{ request('group_by') == 'year' ? 'selected' : '' }}>Year</option>
                                <option value="day" {{ request('group_by') == 'day' ? 'selected' : '' }}>Day</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-chart-bar"></i> Generate Report
                            </button>
                            <button type="button" class="btn btn-outline-secondary" onclick="resetFilters()">
                                <i class="fas fa-times"></i> Reset
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            @if(request('report_type'))
                <!-- Report Summary Cards -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card bg-primary text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h6 class="card-title">{{ ucfirst(str_replace('_', ' ', request('report_type'))) }}</h6>
                                        <h3 class="mb-0">${{ number_format($totalRevenue ?? 0, 2) }}</h3>
                                        <small>Total Revenue</small>
                                    </div>
                                    <div class="align-self-center">
                                        <i class="fas fa-dollar-sign fa-2x"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-success text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h6 class="card-title">Net Profit</h6>
                                        <h3 class="mb-0">${{ number_format($netProfit ?? 0, 2) }}</h3>
                                        <small>{{ $profitMargin ?? 0 }}% Margin</small>
                                    </div>
                                    <div class="align-self-center">
                                        <i class="fas fa-chart-line fa-2x"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-warning text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h6 class="card-title">Expenses</h6>
                                        <h3 class="mb-0">${{ number_format($totalExpenses ?? 0, 2) }}</h3>
                                        <small>Total Expenses</small>
                                    </div>
                                    <div class="align-self-center">
                                        <i class="fas fa-money-bill-wave fa-2x"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-info text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h6 class="card-title">Transactions</h6>
                                        <h3 class="mb-0">{{ $transactionCount ?? 0 }}</h3>
                                        <small>Total Transactions</small>
                                    </div>
                                    <div class="align-self-center">
                                        <i class="fas fa-exchange-alt fa-2x"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Report Content -->
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5>{{ ucfirst(str_replace('_', ' ', request('report_type'))) }} Report</h5>
                            <div class="btn-group btn-group-sm">
                                <button class="btn btn-outline-secondary" onclick="changeView('table')">
                                    <i class="fas fa-table"></i> Table
                                </button>
                                <button class="btn btn-outline-secondary" onclick="changeView('chart')">
                                    <i class="fas fa-chart-bar"></i> Chart
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        @include('reports.partials.' . request('report_type'))
                    </div>
                </div>

                <!-- Quick Insights -->
                <div class="row mt-4">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h6><i class="fas fa-lightbulb"></i> Key Insights</h6>
                            </div>
                            <div class="card-body">
                                @if(request('report_type') == 'profit_loss')
                                    <ul class="list-unstyled">
                                        @if(($profitMargin ?? 0) > 20)
                                            <li><i class="fas fa-check text-success"></i> Excellent profit margin ({{ $profitMargin }}%)</li>
                                        @elseif(($profitMargin ?? 0) > 10)
                                            <li><i class="fas fa-check text-info"></i> Good profit margin ({{ $profitMargin }}%)</li>
                                        @else
                                            <li><i class="fas fa-exclamation text-warning"></i> Consider improving profit margin ({{ $profitMargin }}%)</li>
                                        @endif
                                        
                                        @if(($growthRate ?? 0) > 0)
                                            <li><i class="fas fa-arrow-up text-success"></i> Revenue growth: {{ $growthRate }}%</li>
                                        @else
                                            <li><i class="fas fa-arrow-down text-danger"></i> Revenue decline: {{ abs($growthRate ?? 0) }}%</li>
                                        @endif
                                    </ul>
                                @elseif(request('report_type') == 'aging_report')
                                    <ul class="list-unstyled">
                                        @if(($overdueAmount ?? 0) > 0)
                                            <li><i class="fas fa-exclamation text-danger"></i> ${{ number_format($overdueAmount, 2) }} overdue</li>
                                        @endif
                                        @if(($overdueInvoices ?? 0) > 0)
                                            <li><i class="fas fa-clock text-warning"></i> {{ $overdueInvoices }} overdue invoices</li>
                                        @endif
                                    </ul>
                                @else
                                    <p class="text-muted">Select a report type to view insights</p>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h6><i class="fas fa-download"></i> Quick Actions</h6>
                            </div>
                            <div class="card-body">
                                <div class="d-grid gap-2">
                                    <button class="btn btn-outline-primary btn-sm" onclick="exportPDF()">
                                        <i class="fas fa-file-pdf"></i> Export as PDF
                                    </button>
                                    <button class="btn btn-outline-success btn-sm" onclick="exportExcel()">
                                        <i class="fas fa-file-excel"></i> Export as Excel
                                    </button>
                                    <button class="btn btn-outline-info btn-sm" onclick="emailReport()">
                                        <i class="fas fa-envelope"></i> Email Report
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <!-- Welcome to Reports -->
                <div class="card">
                    <div class="card-body text-center py-5">
                        <i class="fas fa-chart-line fa-5x text-muted mb-4"></i>
                        <h3 class="text-muted">Welcome to Financial Reports</h3>
                        <p class="text-muted mb-4">Select a report type and date range to generate your financial reports.</p>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="card bg-primary text-white">
                                    <div class="card-body text-center">
                                        <i class="fas fa-chart-line fa-2x mb-2"></i>
                                        <h6>Profit & Loss</h6>
                                        <p class="small">Track revenue, expenses, and profits</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-success text-white">
                                    <div class="card-body text-center">
                                        <i class="fas fa-money-bill-wave fa-2x mb-2"></i>
                                        <h6>Cash Flow</h6>
                                        <p class="small">Monitor cash inflows and outflows</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-warning text-white">
                                    <div class="card-body text-center">
                                        <i class="fas fa-balance-scale fa-2x mb-2"></i>
                                        <h6>Balance Sheet</h6>
                                        <p class="small">View assets, liabilities, and equity</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-info text-white">
                                    <div class="card-body text-center">
                                        <i class="fas fa-users fa-2x mb-2"></i>
                                        <h6>Customer Reports</h6>
                                        <p class="small">Analyze customer performance</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

@section('scripts')
<script>
function updateReportType() {
    const reportType = document.getElementById('report_type').value;
    const groupBySelect = document.getElementById('group_by');
    
    // Reset group by options based on report type
    groupBySelect.innerHTML = `
        <option value="month">Month</option>
        <option value="quarter">Quarter</option>
        <option value="year">Year</option>
        <option value="day">Day</option>
    `;
    
    if (reportType === 'aging_report') {
        groupBySelect.innerHTML = `
            <option value="day">Day</option>
            <option value="week">Week</option>
            <option value="month">Month</option>
        `;
    }
}

function resetFilters() {
    document.getElementById('report_type').value = 'profit_loss';
    document.getElementById('date_from').value = '';
    document.getElementById('date_to').value = '';
    document.getElementById('group_by').value = 'month';
    window.location.href = '{{ route("reports.index") }}';
}

function changeView(view) {
    const url = new URL(window.location);
    url.searchParams.set('view', view);
    window.location.href = url.toString();
}

function exportReport() {
    const url = new URL(window.location);
    url.searchParams.set('export', 'true');
    window.open(url.toString(), '_blank');
}

function exportPDF() {
    const url = new URL(window.location);
    url.searchParams.set('export', 'pdf');
    window.open(url.toString(), '_blank');
}

function exportExcel() {
    const url = new URL(window.location);
    url.searchParams.set('export', 'excel');
    window.open(url.toString(), '_blank');
}

function emailReport() {
    if (confirm('Would you like to email this report?')) {
        // Implement email functionality
        alert('Email functionality will be implemented soon.');
    }
}

// Initialize Chart.js if view is chart
@if(request('view') === 'chart' && request('report_type'))
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('reportChart');
    if (ctx) {
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: @json($chartLabels ?? []),
                datasets: [{
                    label: 'Revenue',
                    data: @json($revenueData ?? []),
                    borderColor: 'rgb(75, 192, 192)',
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    tension: 0.1
                }, {
                    label: 'Expenses',
                    data: @json($expenseData ?? []),
                    borderColor: 'rgb(255, 99, 132)',
                    backgroundColor: 'rgba(255, 99, 132, 0.2)',
                    tension: 0.1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    title: {
                        display: true,
                        text: '{{ ucfirst(str_replace("_", " ", request("report_type"))) }} Chart'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return '$' + value.toLocaleString();
                            }
                        }
                    }
                }
            }
        });
    }
});
@endif
@endsection
@endsection