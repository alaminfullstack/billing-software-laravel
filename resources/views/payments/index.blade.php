@extends('layouts.app')

@section('title', 'Payments')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1><i class="fas fa-money-bill"></i> Payments</h1>
                <a href="{{ route('payments.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Record Payment
                </a>
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <!-- Filters -->
            <div class="card mb-4">
                <div class="card-body">
                    <form method="GET" action="{{ route('payments.index') }}" class="row g-3">
                        <div class="col-md-3">
                            <label for="status" class="form-label">Status</label>
                            <select name="status" id="status" class="form-select">
                                <option value="">All Statuses</option>
                                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>Failed</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="payment_method" class="form-label">Payment Method</label>
                            <select name="payment_method" id="payment_method" class="form-select">
                                <option value="">All Methods</option>
                                <option value="cash" {{ request('payment_method') == 'cash' ? 'selected' : '' }}>Cash</option>
                                <option value="check" {{ request('payment_method') == 'check' ? 'selected' : '' }}>Check</option>
                                <option value="credit_card" {{ request('payment_method') == 'credit_card' ? 'selected' : '' }}>Credit Card</option>
                                <option value="bank_transfer" {{ request('payment_method') == 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
                                <option value="paypal" {{ request('payment_method') == 'paypal' ? 'selected' : '' }}>PayPal</option>
                                <option value="stripe" {{ request('payment_method') == 'stripe' ? 'selected' : '' }}>Stripe</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="date_from" class="form-label">Date From</label>
                            <input type="date" name="date_from" id="date_from" class="form-control" value="{{ request('date_from') }}">
                        </div>
                        <div class="col-md-3">
                            <label for="date_to" class="form-label">Date To</label>
                            <input type="date" name="date_to" id="date_to" class="form-control" value="{{ request('date_to') }}">
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-outline-primary">
                                <i class="fas fa-filter"></i> Filter
                            </button>
                            <a href="{{ route('payments.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times"></i> Clear
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Payment Statistics -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card bg-primary text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="card-title">Total Payments</h6>
                                    <h3 class="mb-0">{{ $totalPayments ?? 0 }}</h3>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-money-bill fa-2x"></i>
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
                                    <h6 class="card-title">Completed</h6>
                                    <h3 class="mb-0">{{ $completedPayments ?? 0 }}</h3>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-check-circle fa-2x"></i>
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
                                    <h6 class="card-title">Pending</h6>
                                    <h3 class="mb-0">{{ $pendingPayments ?? 0 }}</h3>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-clock fa-2x"></i>
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
                                    <h6 class="card-title">Total Amount</h6>
                                    <h3 class="mb-0">${{ number_format($totalAmount ?? 0, 2) }}</h3>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-dollar-sign fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payments Table -->
            <div class="card">
                <div class="card-body">
                    @if(isset($payments) && count($payments) > 0)
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>Payment #</th>
                                        <th>Invoice</th>
                                        <th>Customer</th>
                                        <th>Date</th>
                                        <th>Amount</th>
                                        <th>Method</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($payments as $payment)
                                        <tr>
                                            <td>
                                                <strong>{{ $payment->payment_number ?? 'N/A' }}</strong>
                                            </td>
                                            <td>
                                                @if($payment->invoice)
                                                    <a href="{{ route('invoices.show', $payment->invoice) }}" class="text-decoration-none">
                                                        {{ $payment->invoice->invoice_number }}
                                                    </a>
                                                @else
                                                    <span class="text-muted">N/A</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($payment->customer)
                                                    <a href="{{ route('customers.show', $payment->customer) }}" class="text-decoration-none">
                                                        {{ $payment->customer->name }}
                                                    </a>
                                                @elseif($payment->invoice && $payment->invoice->customer)
                                                    <a href="{{ route('customers.show', $payment->invoice->customer) }}" class="text-decoration-none">
                                                        {{ $payment->invoice->customer->name }}
                                                    </a>
                                                @else
                                                    <span class="text-muted">N/A</span>
                                                @endif
                                            </td>
                                            <td>{{ $payment->payment_date->format('M d, Y') }}</td>
                                            <td>
                                                <strong class="text-success">${{ number_format($payment->amount, 2) }}</strong>
                                            </td>
                                            <td>
                                                @php
                                                    $methodLabels = [
                                                        'cash' => 'Cash',
                                                        'check' => 'Check',
                                                        'credit_card' => 'Credit Card',
                                                        'bank_transfer' => 'Bank Transfer',
                                                        'paypal' => 'PayPal',
                                                        'stripe' => 'Stripe'
                                                    ];
                                                @endphp
                                                {{ $methodLabels[$payment->payment_method] ?? ucfirst(str_replace('_', ' ', $payment->payment_method)) }}
                                            </td>
                                            <td>
                                                @php
                                                    $statusClass = [
                                                        'completed' => 'success',
                                                        'pending' => 'warning',
                                                        'failed' => 'danger'
                                                    ][$payment->status] ?? 'secondary';
                                                @endphp
                                                <span class="badge bg-{{ $statusClass }}">{{ ucfirst($payment->status) }}</span>
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('payments.show', $payment) }}" 
                                                       class="btn btn-sm btn-outline-primary" title="View">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('payments.edit', $payment) }}" 
                                                       class="btn btn-sm btn-outline-warning" title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    @if($payment->status == 'pending')
                                                        <form method="POST" action="{{ route('payments.complete', $payment) }}" 
                                                              style="display: inline;">
                                                            @csrf
                                                            <button type="submit" class="btn btn-sm btn-outline-success" 
                                                                    title="Mark as Complete"
                                                                    onclick="return confirm('Mark this payment as completed?')">
                                                                <i class="fas fa-check"></i>
                                                            </button>
                                                        </form>
                                                    @endif
                                                    <form method="POST" action="{{ route('payments.destroy', $payment) }}" 
                                                          style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this payment?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        @if(method_exists($payments, 'links'))
                            <div class="d-flex justify-content-center">
                                {{ $payments->links() }}
                            </div>
                        @endif
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-money-bill fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No payments found</h5>
                            <p class="text-muted">Record your first payment to get started.</p>
                            <a href="{{ route('payments.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Record Payment
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection