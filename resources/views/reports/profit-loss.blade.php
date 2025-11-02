@extends('layouts.app')

@section('title', 'Profit & Loss Report')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1>Profit & Loss Report</h1>
                <a href="{{ route('reports.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Reports
                </a>
            </div>

            <!-- Date Range Filter -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Filter Options</h5>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('reports.profit-loss') }}">
                        <div class="row">
                            <div class="col-md-4">
                                <label for="start_date" class="form-label">Start Date</label>
                                <input type="date" class="form-control" id="start_date" name="start_date" 
                                       value="{{ $startDate }}">
                            </div>
                            <div class="col-md-4">
                                <label for="end_date" class="form-label">End Date</label>
                                <input type="date" class="form-control" id="end_date" name="end_date" 
                                       value="{{ $endDate }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">&nbsp;</label>
                                <div>
                                    <button type="submit" class="btn btn-primary me-2">
                                        <i class="fas fa-filter"></i> Apply Filter
                                    </button>
                                    <a href="{{ route('reports.profit-loss') }}" class="btn btn-outline-secondary">
                                        <i class="fas fa-refresh"></i> Reset
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Summary Cards -->
            <div class="row mb-4">
                <div class="col-md-4">
                    <div class="card bg-success text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h4 class="card-title">Total Revenue</h4>
                                    <h2 class="mb-0">${{ number_format($revenue, 2) }}</h2>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-arrow-up fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card bg-danger text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h4 class="card-title">Total Expenses</h4>
                                    <h2 class="mb-0">${{ number_format($expenses, 2) }}</h2>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-arrow-down fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card @if($profit >= 0) bg-primary text-white @else bg-warning text-dark @endif">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h4 class="card-title">Net {{ $profit >= 0 ? 'Profit' : 'Loss' }}</h4>
                                    <h2 class="mb-0">${{ number_format(abs($profit), 2) }}</h2>
                                    @if($profit >= 0)
                                        <small class="opacity-75">Profitable</small>
                                    @else
                                        <small class="opacity-75">Loss</small>
                                    @endif
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-chart-line fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Profit/Loss Ratio -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Financial Health</h5>
                        </div>
                        <div class="card-body">
                            @php
                                $profitMargin = $revenue > 0 ? ($profit / $revenue) * 100 : 0;
                                $expenseRatio = $revenue > 0 ? ($expenses / $revenue) * 100 : 0;
                            @endphp
                            <div class="row">
                                <div class="col-6">
                                    <div class="text-center">
                                        <h4 class="text-success">{{ number_format($profitMargin, 1) }}%</h4>
                                        <small class="text-muted">Profit Margin</small>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="text-center">
                                        <h4 class="text-danger">{{ number_format($expenseRatio, 1) }}%</h4>
                                        <small class="text-muted">Expense Ratio</small>
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <div class="progress mb-2">
                                <div class="progress-bar bg-success" 
                                     style="width: {{ max(0, $profitMargin) }}%">
                                </div>
                                <div class="progress-bar bg-danger" 
                                     style="width: {{ $expenseRatio }}%">
                                </div>
                            </div>
                            <div class="d-flex justify-content-between">
                                <small class="text-success">Profit</small>
                                <small class="text-danger">Expenses</small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Performance Metrics</h5>
                        </div>
                        <div class="card-body">
                            <table class="table table-sm">
                                <tr>
                                    <td>Revenue per $1 Expense:</td>
                                    <td class="text-end">
                                        <strong>
                                            {{ $expenses > 0 ? number_format($revenue / $expenses, 2) : 'N/A' }}
                                        </strong>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Break-even Point:</td>
                                    <td class="text-end">
                                        <strong>${{ number_format($expenses, 2) }}</strong>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Current Position:</td>
                                    <td class="text-end">
                                        @if($revenue > $expenses)
                                            <span class="text-success"><strong>Above Break-even</strong></span>
                                        @elseif($revenue == $expenses)
                                            <span class="text-warning"><strong>At Break-even</strong></span>
                                        @else
                                            <span class="text-danger"><strong>Below Break-even</strong></span>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            @if(count($monthlyBreakdown) > 0)
                <!-- Monthly Trend Chart -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Monthly Profit & Loss Trend</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="profitLossChart" height="100"></canvas>
                    </div>
                </div>

                <!-- Monthly Breakdown Table -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Monthly Breakdown</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Month</th>
                                        <th>Revenue</th>
                                        <th>Expenses</th>
                                        <th>Profit/Loss</th>
                                        <th>Margin %</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($monthlyBreakdown as $month)
                                        <tr class="@if($month['profit'] >= 0) table-success @else table-danger @endif">
                                            <td><strong>{{ $month['month'] }}</strong></td>
                                            <td>${{ number_format($month['revenue'], 2) }}</td>
                                            <td>${{ number_format($month['expenses'], 2) }}</td>
                                            <td>
                                                <strong>
                                                    ${{ number_format(abs($month['profit']), 2) }}
                                                    {{ $month['profit'] >= 0 ? 'Profit' : 'Loss' }}
                                                </strong>
                                            </td>
                                            <td>
                                                @php
                                                    $monthMargin = $month['revenue'] > 0 ? ($month['profit'] / $month['revenue']) * 100 : 0;
                                                @endphp
                                                <span class="badge {{ $monthMargin >= 0 ? 'bg-success' : 'bg-danger' }}">
                                                    {{ number_format($monthMargin, 1) }}%
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr class="table-primary">
                                        <th>Total</th>
                                        <th>${{ number_format($revenue, 2) }}</th>
                                        <th>${{ number_format($expenses, 2) }}</th>
                                        <th>
                                            ${{ number_format(abs($profit), 2) }}
                                            {{ $profit >= 0 ? 'Profit' : 'Loss' }}
                                        </th>
                                        <th>
                                            <span class="badge {{ $profitMargin >= 0 ? 'bg-success' : 'bg-danger' }}">
                                                {{ number_format($profitMargin, 1) }}%
                                            </span>
                                        </th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            @else
                <div class="card">
                    <div class="card-body text-center py-5">
                        <i class="fas fa-chart-bar fa-3x text-muted mb-3"></i>
                        <h5>No Financial Data Found</h5>
                        <p class="text-muted">No revenue or expense data found for the selected date range.</p>
                        <div class="row justify-content-center">
                            <div class="col-md-6">
                                <a href="{{ route('invoices.create') }}" class="btn btn-success me-2">
                                    <i class="fas fa-plus"></i> Create Invoice
                                </a>
                                <a href="{{ route('expenses.create') }}" class="btn btn-danger">
                                    <i class="fas fa-plus"></i> Record Expense
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

@if(count($monthlyBreakdown) > 0)
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('profitLossChart').getContext('2d');
    
    const monthlyData = @json($monthlyBreakdown);
    
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: monthlyData.map(item => item.month),
            datasets: [{
                label: 'Revenue',
                data: monthlyData.map(item => item.revenue),
                backgroundColor: 'rgba(75, 192, 192, 0.8)',
                borderColor: 'rgba(75, 192, 192, 1)',
                borderWidth: 1
            }, {
                label: 'Expenses',
                data: monthlyData.map(item => item.expenses),
                backgroundColor: 'rgba(255, 99, 132, 0.8)',
                borderColor: 'rgba(255, 99, 132, 1)',
                borderWidth: 1
            }, {
                label: 'Profit/Loss',
                data: monthlyData.map(item => item.profit),
                type: 'line',
                backgroundColor: function(context) {
                    return context.parsed.y >= 0 ? 'rgba(40, 167, 69, 0.8)' : 'rgba(220, 53, 69, 0.8)';
                },
                borderColor: function(context) {
                    return context.parsed.y >= 0 ? 'rgba(40, 167, 69, 1)' : 'rgba(220, 53, 69, 1)';
                },
                borderWidth: 2,
                fill: false,
                tension: 0.1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return '$' + value.toLocaleString();
                        }
                    }
                }
            },
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return context.dataset.label + ': $' + context.parsed.y.toLocaleString();
                        }
                    }
                }
            }
        }
    });
});
</script>
@endif
@endsection
