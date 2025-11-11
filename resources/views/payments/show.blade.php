@extends('layouts.app')

@section('title', 'Payment Details')

@section('content')
<div class="container-fluid">
    @if(isset($payment))
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1><i class="fas fa-money-bill"></i> Payment #{{ $payment->payment_number ?? 'N/A' }}</h1>
                    <div>
                        <a href="{{ route('payments.index') }}" class="btn btn-outline-secondary me-2">
                            <i class="fas fa-arrow-left"></i> Back to Payments
                        </a>
                        <div class="btn-group" role="group">
                            <a href="{{ route('payments.edit', $payment) }}" class="btn btn-warning">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            @if($payment->status == 'pending')
                                <form method="POST" action="{{ route('payments.complete', $payment) }}" style="display: inline;">
                                    @csrf
                                    <button type="submit" class="btn btn-success"
                                            onclick="return confirm('Mark this payment as completed?')">
                                        <i class="fas fa-check"></i> Mark Complete
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-8">
                        <!-- Payment Information -->
                        <div class="card">
                            <div class="card-header">
                                <h5><i class="fas fa-info-circle"></i> Payment Information</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <h6 class="text-muted">Payment Number</h6>
                                        <p class="fw-bold">{{ $payment->payment_number ?? 'N/A' }}</p>
                                    </div>
                                    <div class="col-md-6">
                                        <h6 class="text-muted">Status</h6>
                                        <p>
                                            @php
                                                $statusClass = [
                                                    'completed' => 'success',
                                                    'pending' => 'warning',
                                                    'failed' => 'danger'
                                                ][$payment->status] ?? 'secondary';
                                            @endphp
                                            <span class="badge bg-{{ $statusClass }}">{{ ucfirst($payment->status) }}</span>
                                        </p>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <h6 class="text-muted">Payment Date</h6>
                                        <p>{{ \Carbon\Carbon::parse($payment->payment_date)->format('M d, Y') }}</p>
                                    </div>
                                    <div class="col-md-6">
                                        <h6 class="text-muted">Payment Method</h6>
                                        <p>
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
                                        </p>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <h6 class="text-muted">Amount</h6>
                                        <p class="h4 text-success">${{ number_format($payment->amount, 2) }}</p>
                                    </div>
                                    <div class="col-md-6">
                                        <h6 class="text-muted">Reference Number</h6>
                                        <p>{{ $payment->reference_number ?? 'N/A' }}</p>
                                    </div>
                                </div>

                                @if($payment->notes)
                                    <div class="row">
                                        <div class="col-12">
                                            <h6 class="text-muted">Notes</h6>
                                            <p>{{ $payment->notes }}</p>
                                        </div>
                                    </div>
                                @endif

                                @if($payment->payment_method == 'credit_card' || $payment->payment_method == 'stripe')
                                    <div class="row">
                                        <div class="col-md-6">
                                            <h6 class="text-muted">Transaction ID</h6>
                                            <p>{{ $payment->transaction_id ?? 'N/A' }}</p>
                                        </div>
                                        <div class="col-md-6">
                                            <h6 class="text-muted">Processing Fee</h6>
                                            <p>${{ number_format($payment->processing_fee ?? 0, 2) }}</p>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Related Invoice -->
                        @if($payment->invoice)
                            <div class="card mt-4">
                                <div class="card-header">
                                    <h5><i class="fas fa-file-invoice"></i> Related Invoice</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <h6 class="text-muted">Invoice Number</h6>
                                            <p class="fw-bold">
                                                <a href="{{ route('invoices.show', $payment->invoice) }}" class="text-decoration-none">
                                                    {{ $payment->invoice->invoice_number }}
                                                </a>
                                            </p>
                                        </div>
                                        <div class="col-md-6">
                                            <h6 class="text-muted">Invoice Date</h6>
                                            <p>{{ \Carbon\Carbon::parse($payment->invoice->invoice_date)->format('M d, Y') }}</p>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <h6 class="text-muted">Invoice Total</h6>
                                            <p class="fw-bold">${{ number_format($payment->invoice->total, 2) }}</p>
                                        </div>
                                        <div class="col-md-6">
                                            <h6 class="text-muted">Invoice Status</h6>
                                            <p>
                                                @php
                                                    $invoiceStatusClass = [
                                                        'draft' => 'secondary',
                                                        'sent' => 'info',
                                                        'paid' => 'success',
                                                        'overdue' => 'danger'
                                                    ][$payment->invoice->status] ?? 'secondary';
                                                @endphp
                                                <span class="badge bg-{{ $invoiceStatusClass }}">{{ ucfirst($payment->invoice->status) }}</span>
                                            </p>
                                        </div>
                                    </div>

                                    <div class="mt-3">
                                        <a href="{{ route('invoices.show', $payment->invoice) }}" class="btn btn-outline-primary btn-sm">
                                            <i class="fas fa-eye"></i> View Invoice Details
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- Customer Information -->
                        @if($payment->customer || ($payment->invoice && $payment->invoice->customer))
                            @php
                                $customer = $payment->customer ?? ($payment->invoice->customer ?? null);
                            @endphp
                            @if($customer)
                                <div class="card mt-4">
                                    <div class="card-header">
                                        <h5><i class="fas fa-user"></i> Customer Information</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <h6 class="text-muted">Customer Name</h6>
                                                <p class="fw-bold">{{ $customer->name }}</p>
                                            </div>
                                            <div class="col-md-6">
                                                <h6 class="text-muted">Email</h6>
                                                <p>{{ $customer->email }}</p>
                                            </div>
                                        </div>
                                        @if($customer->phone)
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <h6 class="text-muted">Phone</h6>
                                                    <p>{{ $customer->phone }}</p>
                                                </div>
                                                <div class="col-md-6">
                                                    <h6 class="text-muted">Address</h6>
                                                    <p>{{ $customer->address ?? 'N/A' }}</p>
                                                </div>
                                            </div>
                                        @endif
                                        <div class="mt-3">
                                            <a href="{{ route('customers.show', $customer) }}" class="btn btn-outline-primary btn-sm">
                                                <i class="fas fa-eye"></i> View Customer Details
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endif
                    </div>

                    <div class="col-md-4">
                        <!-- Payment Summary -->
                        <div class="card">
                            <div class="card-header">
                                <h5><i class="fas fa-calculator"></i> Payment Summary</h5>
                            </div>
                            <div class="card-body">
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Payment Amount:</span>
                                    <strong class="text-success">${{ number_format($payment->amount, 2) }}</strong>
                                </div>
                                
                                @if(($payment->processing_fee ?? 0) > 0)
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>Processing Fee:</span>
                                        <strong>${{ number_format($payment->processing_fee, 2) }}</strong>
                                    </div>
                                    
                                    <div class="d-flex justify-content-between">
                                        <span>Net Amount:</span>
                                        <strong>${{ number_format($payment->amount - ($payment->processing_fee ?? 0), 2) }}</strong>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Quick Actions -->
                        <div class="card mt-4">
                            <div class="card-header">
                                <h5><i class="fas fa-bolt"></i> Quick Actions</h5>
                            </div>
                            <div class="card-body">
                                @if($payment->status == 'pending')
                                    <form method="POST" action="{{ route('payments.complete', $payment) }}" class="mb-2">
                                        @csrf
                                        <button type="submit" class="btn btn-success w-100"
                                                onclick="return confirm('Mark this payment as completed?')">
                                            <i class="fas fa-check"></i> Mark as Completed
                                        </button>
                                    </form>
                                @endif
                                
                                <a href="{{ route('payments.edit', $payment) }}" class="btn btn-warning w-100 mb-2">
                                    <i class="fas fa-edit"></i> Edit Payment
                                </a>

                                @if($payment->invoice)
                                    <a href="{{ route('invoices.show', $payment->invoice) }}" class="btn btn-outline-primary w-100 mb-2">
                                        <i class="fas fa-file-invoice"></i> View Invoice
                                    </a>
                                @endif

                                @if($customer)
                                    <a href="{{ route('customers.show', $customer) }}" class="btn btn-outline-info w-100 mb-2">
                                        <i class="fas fa-user"></i> View Customer
                                    </a>
                                @endif

                                <div class="btn-group w-100">
                                    <a href="" class="btn btn-outline-secondary">
                                        <i class="fas fa-print"></i> Print
                                    </a>
                                    <a href="" class="btn btn-outline-secondary">
                                        <i class="fas fa-download"></i> PDF
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Payment Timeline -->
                        <div class="card mt-4">
                            <div class="card-header">
                                <h5><i class="fas fa-clock"></i> Payment Timeline</h5>
                            </div>
                            <div class="card-body">
                                <div class="timeline">
                                    <div class="timeline-item">
                                        <div class="timeline-marker bg-primary"></div>
                                        <div class="timeline-content">
                                            <h6 class="timeline-title">Payment Created</h6>
                                            <p class="timeline-text">
                                                {{ $payment->created_at->format('M d, Y H:i') }}
                                            </p>
                                        </div>
                                    </div>
                                    
                                    @if($payment->status == 'completed')
                                        <div class="timeline-item">
                                            <div class="timeline-marker bg-success"></div>
                                            <div class="timeline-content">
                                                <h6 class="timeline-title">Payment Completed</h6>
                                                <p class="timeline-text">
                                                    {{ $payment->updated_at->format('M d, Y H:i') }}
                                                </p>
                                            </div>
                                        </div>
                                    @endif
                                    
                                    @if($payment->status == 'failed')
                                        <div class="timeline-item">
                                            <div class="timeline-marker bg-danger"></div>
                                            <div class="timeline-content">
                                                <h6 class="timeline-title">Payment Failed</h6>
                                                <p class="timeline-text">
                                                    {{ $payment->updated_at->format('M d, Y H:i') }}
                                                </p>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="alert alert-danger">
            <h4>Payment not found</h4>
            <p>The requested payment could not be found.</p>
            <a href="{{ route('payments.index') }}" class="btn btn-primary">Back to Payments</a>
        </div>
    @endif
</div>

<style>
.timeline {
    position: relative;
    padding-left: 20px;
}

.timeline-item {
    position: relative;
    margin-bottom: 20px;
}

.timeline-marker {
    position: absolute;
    left: -26px;
    top: 5px;
    width: 12px;
    height: 12px;
    border-radius: 50%;
}

.timeline-content {
    margin-left: 20px;
}

.timeline-title {
    font-size: 0.9rem;
    font-weight: 600;
    margin-bottom: 2px;
}

.timeline-text {
    font-size: 0.8rem;
    color: #6c757d;
    margin: 0;
}
</style>
@endsection