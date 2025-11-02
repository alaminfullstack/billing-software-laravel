@extends('layouts.app')

@section('title', 'Expense Report')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1>Expense Report</h1>
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
                    <form method="GET" action="{{ route('reports.expenses') }}">
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
                                    <a href="{{ route('reports.expenses') }}" class="btn btn-outline-secondary">
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
                    <div class="card bg-danger text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h4 class="card-title">Total Expenses</h4>
                                    <h2 class="mb-0">${{ number_format($totalExpenses, 2) }}</h2>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-money-bill-wave fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card bg-warning text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h4 class="card-title">Expense Entries</h4>
                                    <h2 class="mb-0">{{ $expenseData->sum('count') }}</h2>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-receipt fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card bg-info text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h4 class="card-title">Avg per Entry</h4>
                                    <h2 class="mb-0">${{ number_format($expenseData->sum('count') > 0 ? $totalExpenses / $expenseData->sum('count') : 0, 2) }}</h2>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-calculator fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @if($expenseData->count() > 0)
                <!-- Daily Expenses Chart -->
                <div class="row mb-4">
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Daily Expenses Trend</h5>
                            </div>
                            <div class="card-body">
                                <canvas id="expensesChart" height="100"></canvas>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Category Breakdown -->
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Category Breakdown</h5>
                            </div>
                            <div class="card-body">
                                <canvas id="categoryChart" height="200"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Expense Data Table -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Daily Expense Breakdown</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Expense Count</th>
                                        <th>Total Amount</th>
                                        <th>Average per Expense</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($expenseData as $data)
                                        <tr>
                                            <td>{{ \Carbon\Carbon::parse($data->date)->format('M d, Y') }}</td>
                                            <td>{{ $data->count }}</td>
                                            <td>${{ number_format($data->total, 2) }}</td>
                                            <td>${{ number_format($data->count > 0 ? $data->total / $data->count : 0, 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr class="table-danger">
                                        <th>Total</th>
                                        <th>{{ $expenseData->sum('count') }}</th>
                                        <th>${{ number_format($totalExpenses, 2) }}</th>
                                        <th>${{ number_format($expenseData->sum('count') > 0 ? $totalExpenses / $expenseData->sum('count') : 0, 2) }}</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Category-wise Expenses -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Expenses by Category</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Category</th>
                                        <th>Count</th>
                                        <th>Total Amount</th>
                                        <th>Percentage</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($categoryExpenses as $data)
                                        <tr>
                                            <td>
                                                <strong>{{ $data->category->name ?? 'Unknown Category' }}</strong>
                                            </td>
                                            <td>{{ $data->count }}</td>
                                            <td>${{ number_format($data->total, 2) }}</td>
                                            <td>
                                                @php
                                                    $percentage = $totalExpenses > 0 ? ($data->total / $totalExpenses) * 100 : 0;
                                                @endphp
                                                <div class="progress">
                                                    <div class="progress-bar" 
                                                         style="width: {{ $percentage }}%">
                                                        {{ number_format($percentage, 1) }}%
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <a href="{{ route('expenses.index') }}?category_id={{ $data->expense_category_id }}" 
                                                   class="btn btn-sm btn-outline-info">
                                                    <i class="fas fa-eye"></i> View
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @else
                <div class="card">
                    <div class="card-body text-center py-5">
                        <i class="fas fa-money-bill-wave fa-3x text-muted mb-3"></i>
                        <h5>No Expense Data Found</h5>
                        <p class="text-muted">No expenses found for the selected date range.</p>
                        <a href="{{ route('expenses.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Record Expense
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

@if($expenseData->count() > 0)
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Daily Expenses Chart
    const dailyCtx = document.getElementById('expensesChart').getContext('2d');
    
    const dailyData = @json($expenseData->map(function($item) {
        return [
            'date' => \Carbon\Carbon::parse($item->date)->format('M d'),
            'total' => $item->total,
            'count' => $item->count
        ];
    }));
    
    new Chart(dailyCtx, {
        type: 'line',
        data: {
            labels: dailyData.map(item => item.date),
            datasets: [{
                label: 'Daily Expenses',
                data: dailyData.map(item => item.total),
                borderColor: 'rgb(255, 99, 132)',
                backgroundColor: 'rgba(255, 99, 132, 0.1)',
                borderWidth: 2,
                fill: true,
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
                            return 'Expense: $' + context.parsed.y.toLocaleString();
                        }
                    }
                }
            }
        }
    });
    
    // Category Chart
    const categoryCtx = document.getElementById('categoryChart').getContext('2d');
    const categoryData = @json($categoryExpenses->map(function($item) {
        return [
            'name' => $item->category->name ?? 'Unknown',
            'total' => $item->total
        ];
    }));
    
    const colors = [
        '#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', 
        '#9966FF', '#FF9F40', '#FF6384', '#C9CBCF'
    ];
    
    new Chart(categoryCtx, {
        type: 'doughnut',
        data: {
            labels: categoryData.map(item => item.name),
            datasets: [{
                data: categoryData.map(item => item.total),
                backgroundColor: colors.slice(0, categoryData.length),
                borderWidth: 2,
                borderColor: '#fff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const total = categoryData.reduce((sum, item) => sum + item.total, 0);
                            const percentage = ((context.parsed / total) * 100).toFixed(1);
                            return context.label + ': $' + context.parsed.toLocaleString() + ' (' + percentage + '%)';
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
