@extends('layouts.app')

@section('title', 'Aging Report')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1><i class="fas fa-clock"></i> Aging Report</h1>
                <div class="btn-group">
                    <button type="button" class="btn btn-outline-primary" onclick="window.print()">
                        <i class="fas fa-print"></i> Print
                    </button>
                    <button type="button" class="btn btn-outline-secondary" onclick="exportToPDF()">
                        <i class="fas fa-file-pdf"></i> Export PDF
                    </button>
                    <button type="button" class="btn btn-outline-info" onclick="sendReminders()">
                        <i class="fas fa-envelope"></i> Send Reminders
                    </button>
                </div>
            </div>

            <!-- Filter Options -->
            <div class="card mb-4">
                <div class="card-body">
                    <form method="GET" action="{{ route('reports.aging') }}" class="row g-3">
                        <div class="col-md-3">
                            <label for="report_type" class="form-label">Report Type</label>
                            <select class="form-select" id="report_type" name="report_type">
                                <option value="receivables" {{ request('report_type', 'receivables') == 'receivables' ? 'selected' : '' }}>
                                    Accounts Receivable
                                </option>
                                <option value="payables" {{ request('report_type') == 'payables' ? 'selected' : '' }}>
                                    Accounts Payable
                                </option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="as_of_date" class="form-label">As of Date</label>
                            <input type="date" class="form-control" id="as_of_date" name="as_of_date" 
                                   value="{{ request('as_of_date', date('Y-m-d')) }}">
                        </div>
                        <div class="col-md-3">
                            <label for="customer_filter" class="form-label">Customer/Supplier</label>
                            <select class="form-select" id="customer_filter" name="customer_filter">
                                <option value="">All Customers</option>
                                <option value="overdue">Show Only Overdue</option>
                                <option value="current">Show Only Current</option>
                            </select>
                        </div>
                        <div class="col-md-3 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary me-2">
                                <i class="fas fa-search"></i> Generate Report
                            </button>
                            <a href="{{ route('reports.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left"></i> Back to Reports
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Summary Cards -->
            <div class="row mb-4">
                <div class="col-md-2">
                    <div class="card text-center">
                        <div class="card-body">
                            <h5 class="card-title text-info">
                                <i class="fas fa-clock"></i> Current
                            </h5>
                            <h3 class="text-info" id="current-amount">
                                ${{ number_format($currentAmount ?? 0, 2) }}
                            </h3>
                            <small class="text-muted">0-30 days</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="card text-center">
                        <div class="card-body">
                            <h5 class="card-title text-warning">
                                <i class="fas fa-exclamation-triangle"></i> 31-60 Days
                            </h5>
                            <h3 class="text-warning" id="thirty-to-sixty">
                                ${{ number_format($thirtyToSixty ?? 0, 2) }}
                            </h3>
                            <small class="text-muted">31-60 days overdue</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="card text-center">
                        <div class="card-body">
                            <h5 class="card-title text-danger">
                                <i class="fas fa-times-circle"></i> 61-90 Days
                            </h5>
                            <h3 class="text-danger" id="sixty-to-ninety">
                                ${{ number_format($sixtyToNinety ?? 0, 2) }}
                            </h3>
                            <small class="text-muted">61-90 days overdue</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="card text-center">
                        <div class="card-body">
                            <h5 class="card-title text-dark">
                                <i class="fas fa-ban"></i> 90+ Days
                            </h5>
                            <h3 class="text-dark" id="ninety-plus">
                                ${{ number_format($ninetyPlus ?? 0, 2) }}
                            </h3>
                            <small class="text-muted">Over 90 days overdue</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="card text-center">
                        <div class="card-body">
                            <h5 class="card-title text-primary">
                                <i class="fas fa-money-bill-wave"></i> Total Outstanding
                            </h5>
                            <h3 class="text-primary" id="total-outstanding">
                                ${{ number_format($totalOutstanding ?? 0, 2) }}
                            </h3>
                            <small class="text-muted">Total amount owed</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="card text-center">
                        <div class="card-body">
                            <h5 class="card-title {{ ($ninetyPlus ?? 0) > 0 ? 'text-danger' : 'text-success' }}">
                                <i class="fas fa-chart-line"></i> Risk Level
                            </h5>
                            <h3 class="{{ ($ninetyPlus ?? 0) > 0 ? 'text-danger' : 'text-success' }}" id="risk-level">
                                {{ ($ninetyPlus ?? 0) > 0 ? 'HIGH' : 'LOW' }}
                            </h3>
                            <small class="text-muted">Collection risk</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Aging Analysis Chart -->
            <div class="row mb-4">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <h5><i class="fas fa-chart-bar"></i> Aging Distribution</h5>
                        </div>
                        <div class="card-body">
                            <canvas id="agingChart" width="400" height="200"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <h5><i class="fas fa-exclamation-circle"></i> Collection Alerts</h5>
                        </div>
                        <div class="card-body">
                            @if(($ninetyPlus ?? 0) > 0)
                                <div class="alert alert-danger">
                                    <strong>High Risk Items:</strong> ${{ number_format($ninetyPlus ?? 0, 2) }} are over 90 days overdue.
                                </div>
                            @endif
                            
                            @if(($sixtyToNinety ?? 0) > ($totalOutstanding ?? 0) * 0.3)
                                <div class="alert alert-warning">
                                    <strong>Warning:</strong> Over 30% of outstanding amounts are 61+ days overdue.
                                </div>
                            @endif
                            
                            @if(($currentAmount ?? 0) < ($totalOutstanding ?? 0) * 0.5)
                                <div class="alert alert-info">
                                    <strong>Note:</strong> Less than 50% of outstanding amounts are current.
                                </div>
                            @endif
                            
                            <div class="mt-3">
                                <small class="text-muted">
                                    <strong>Collection Rate:</strong>
                                    @if(($totalOutstanding ?? 0) > 0)
                                        {{ number_format(100 - (($ninetyPlus ?? 0) / ($totalOutstanding ?? 0) * 100), 1) }}% collected within 90 days
                                    @else
                                        N/A
                                    @endif
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Detailed Aging Table -->
            <div class="card">
                <div class="card-header">
                    <h5><i class="fas fa-table"></i> Detailed Aging Report</h5>
                    <p class="mb-0 text-muted">
                        As of: {{ request('as_of_date', date('Y-m-d')) }} | 
                        Report Type: {{ ucfirst(request('report_type', 'receivables')) }}
                    </p>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>{{ request('report_type', 'receivables') == 'payables' ? 'Supplier' : 'Customer' }}</th>
                                    <th>Invoice #</th>
                                    <th>Issue Date</th>
                                    <th>Due Date</th>
                                    <th>Days Overdue</th>
                                    <th>Current (0-30)</th>
                                    <th>31-60 Days</th>
                                    <th>61-90 Days</th>
                                    <th>90+ Days</th>
                                    <th>Total Amount</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                // Sample data for demonstration
                                $agingData = $agingItems ?? collect([
                                    [
                                        'name' => 'ABC Corporation',
                                        'invoice_number' => 'INV-001',
                                        'issue_date' => '2024-09-15',
                                        'due_date' => '2024-10-15',
                                        'amount' => 2500.00,
                                        'days_overdue' => 18,
                                        'customer_id' => 1
                                    ],
                                    [
                                        'name' => 'XYZ Ltd',
                                        'invoice_number' => 'INV-002',
                                        'issue_date' => '2024-08-20',
                                        'due_date' => '2024-09-20',
                                        'amount' => 1750.00,
                                        'days_overdue' => 43,
                                        'customer_id' => 2
                                    ],
                                    [
                                        'name' => 'Tech Solutions Inc',
                                        'invoice_number' => 'INV-003',
                                        'issue_date' => '2024-07-10',
                                        'due_date' => '2024-08-10',
                                        'amount' => 3200.00,
                                        'days_overdue' => 84,
                                        'customer_id' => 3
                                    ],
                                    [
                                        'name' => 'Global Enterprises',
                                        'invoice_number' => 'INV-004',
                                        'issue_date' => '2024-06-01',
                                        'due_date' => '2024-07-01',
                                        'amount' => 890.00,
                                        'days_overdue' => 124,
                                        'customer_id' => 4
                                    ]
                                ]);
                                @endphp
                                
                                @foreach($agingData as $item)
                                <tr class="{{ $item['days_overdue'] > 90 ? 'table-danger' : ($item['days_overdue'] > 60 ? 'table-warning' : ($item['days_overdue'] > 30 ? 'table-info' : '')) }}">
                                    <td>
                                        <strong>{{ $item['name'] }}</strong>
                                        <br>
                                        <small class="text-muted">ID: {{ $item['customer_id'] }}</small>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary">{{ $item['invoice_number'] }}</span>
                                    </td>
                                    <td>{{ date('M d, Y', strtotime($item['issue_date'])) }}</td>
                                    <td>{{ date('M d, Y', strtotime($item['due_date'])) }}</td>
                                    <td>
                                        @if($item['days_overdue'] > 0)
                                            <span class="badge bg-{{ $item['days_overdue'] > 90 ? 'danger' : ($item['days_overdue'] > 60 ? 'warning' : 'info') }}">
                                                {{ $item['days_overdue'] }} days
                                            </span>
                                        @else
                                            <span class="badge bg-success">Current</span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        @if($item['days_overdue'] <= 30)
                                            ${{ number_format($item['amount'], 2) }}
                                        @else
                                            $0.00
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        @if($item['days_overdue'] > 30 && $item['days_overdue'] <= 60)
                                            ${{ number_format($item['amount'], 2) }}
                                        @else
                                            $0.00
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        @if($item['days_overdue'] > 60 && $item['days_overdue'] <= 90)
                                            ${{ number_format($item['amount'], 2) }}
                                        @else
                                            $0.00
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        @if($item['days_overdue'] > 90)
                                            ${{ number_format($item['amount'], 2) }}
                                        @else
                                            $0.00
                                        @endif
                                    </td>
                                    <td class="text-end fw-bold">
                                        ${{ number_format($item['amount'], 2) }}
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <button class="btn btn-outline-primary" title="View Details">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button class="btn btn-outline-info" title="Send Reminder">
                                                <i class="fas fa-envelope"></i>
                                            </button>
                                            @if($item['days_overdue'] > 60)
                                            <button class="btn btn-outline-warning" title="Follow Up">
                                                <i class="fas fa-phone"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                                
                                <!-- Summary Row -->
                                <tr class="table-dark fw-bold">
                                    <td colspan="5">TOTALS</td>
                                    <td class="text-end">${{ number_format($currentAmount ?? 0, 2) }}</td>
                                    <td class="text-end">${{ number_format($thirtyToSixty ?? 0, 2) }}</td>
                                    <td class="text-end">${{ number_format($sixtyToNinety ?? 0, 2) }}</td>
                                    <td class="text-end">${{ number_format($ninetyPlus ?? 0, 2) }}</td>
                                    <td class="text-end">${{ number_format($totalOutstanding ?? 0, 2) }}</td>
                                    <td></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Customer Aging Summary -->
            <div class="row mt-4">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h5><i class="fas fa-users"></i> Top Overdue Customers</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Customer</th>
                                            <th>Overdue Amount</th>
                                            <th>Oldest Invoice</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                        $topOverdue = $topOverdueCustomers ?? collect([
                                            ['name' => 'Global Enterprises', 'amount' => 890.00, 'days' => 124],
                                            ['name' => 'Tech Solutions Inc', 'amount' => 3200.00, 'days' => 84],
                                            ['name' => 'XYZ Ltd', 'amount' => 1750.00, 'days' => 43],
                                        ]);
                                        @endphp
                                        
                                        @foreach($topOverdue as $customer)
                                        <tr>
                                            <td>
                                                <strong>{{ $customer['name'] }}</strong>
                                                <br>
                                                <small class="text-muted">{{ $customer['days'] }} days overdue</small>
                                            </td>
                                            <td class="text-end text-danger">
                                                ${{ number_format($customer['amount'], 2) }}
                                            </td>
                                            <td>
                                                <span class="badge bg-{{ $customer['days'] > 90 ? 'danger' : ($customer['days'] > 60 ? 'warning' : 'info') }}">
                                                    {{ $customer['days'] }}d
                                                </span>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h5><i class="fas fa-tasks"></i> Collection Actions</h5>
                        </div>
                        <div class="card-body">
                            <div class="d-grid gap-2">
                                <button class="btn btn-outline-primary" onclick="generateReport()">
                                    <i class="fas fa-file-export"></i> Generate Collection Letter
                                </button>
                                <button class="btn btn-outline-info" onclick="scheduleFollowUp()">
                                    <i class="fas fa-calendar-plus"></i> Schedule Follow-up Calls
                                </button>
                                <button class="btn btn-outline-warning" onclick="createPaymentPlan()">
                                    <i class="fas fa-handshake"></i> Setup Payment Plans
                                </button>
                                <button class="btn btn-outline-danger" onclick="escalateOverdue()">
                                    <i class="fas fa-exclamation-triangle"></i> Escalate 90+ Day Accounts
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
function exportToPDF() {
    alert('PDF export feature will be implemented.');
}

function sendReminders() {
    if (confirm('Send payment reminders to all overdue customers?')) {
        // Implement reminder sending functionality
        alert('Payment reminders feature will be implemented.');
    }
}

function generateReport() {
    alert('Collection letter generation will be implemented.');
}

function scheduleFollowUp() {
    alert('Follow-up scheduling will be implemented.');
}

function createPaymentPlan() {
    alert('Payment plan setup will be implemented.');
}

function escalateOverdue() {
    if (confirm('Escalate all accounts over 90 days overdue?')) {
        alert('Account escalation will be implemented.');
    }
}

// Initialize aging chart
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('agingChart').getContext('2d');
    // Chart.js implementation would go here
    // For now, just a placeholder
    ctx.font = '14px Arial';
    ctx.fillText('Aging Distribution Chart', 10, 20);
    ctx.fillText('(Chart.js implementation needed)', 10, 40);
});
</script>
@endsection