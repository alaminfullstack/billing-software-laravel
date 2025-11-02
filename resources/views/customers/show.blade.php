@extends('layouts.app')

@section('title', 'Customer Details')

@section('content')
<div class="container-fluid">
    @if(isset($customer))
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1><i class="fas fa-user"></i> {{ $customer->name }}</h1>
                    <div>
                        <a href="{{ route('customers.edit', $customer) }}" class="btn btn-warning me-2">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                        <a href="{{ route('customers.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left"></i> Back to Customers
                        </a>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-8">
                        <!-- Customer Overview -->
                        <div class="card">
                            <div class="card-header">
                                <h5><i class="fas fa-info-circle"></i> Customer Information</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <h6 class="text-muted">Customer Type</h6>
                                        <p>
                                            <span class="badge bg-primary">{{ ucfirst($customer->customer_type ?? 'Individual') }}</span>
                                        </p>
                                    </div>
                                    <div class="col-md-6">
                                        <h6 class="text-muted">Status</h6>
                                        <p>
                                            @php
                                                $statusClass = $customer->status == 'active' ? 'success' : 'secondary';
                                            @endphp
                                            <span class="badge bg-{{ $statusClass }}">{{ ucfirst($customer->status) }}</span>
                                        </p>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <h6 class="text-muted">Email</h6>
                                        <p>
                                            <a href="mailto:{{ $customer->email }}" class="text-decoration-none">
                                                {{ $customer->email }}
                                            </a>
                                        </p>
                                    </div>
                                    <div class="col-md-6">
                                        <h6 class="text-muted">Phone</h6>
                                        <p>{{ $customer->phone ?? 'N/A' }}</p>
                                    </div>
                                </div>

                                @if($customer->company_name)
                                    <div class="row">
                                        <div class="col-md-6">
                                            <h6 class="text-muted">Company</h6>
                                            <p>{{ $customer->company_name }}</p>
                                        </div>
                                        <div class="col-md-6">
                                            <h6 class="text-muted">Website</h6>
                                            @if($customer->website)
                                                <p>
                                                    <a href="{{ $customer->website }}" target="_blank" class="text-decoration-none">
                                                        {{ $customer->website }}
                                                    </a>
                                                </p>
                                            @else
                                                <p>N/A</p>
                                            @endif
                                        </div>
                                    </div>
                                @endif

                                @if($customer->address || $customer->city || $customer->country)
                                    <div class="row">
                                        <div class="col-12">
                                            <h6 class="text-muted">Address</h6>
                                            <p>
                                                @if($customer->address)
                                                    {{ $customer->address }}<br>
                                                @endif
                                                @if($customer->city)
                                                    {{ $customer->city }}
                                                @endif
                                                @if($customer->state)
                                                    , {{ $customer->state }}
                                                @endif
                                                @if($customer->zip_code)
                                                    {{ $customer->zip_code }}
                                                @endif
                                                @if($customer->country && $customer->country != 'US')
                                                    <br>{{ $customer->country }}
                                                @endif
                                            </p>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Financial Summary -->
                        <div class="card mt-4">
                            <div class="card-header">
                                <h5><i class="fas fa-chart-pie"></i> Financial Summary</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="text-center">
                                            <h6 class="text-muted">Total Invoices</h6>
                                            <h3 class="text-primary">{{ $totalInvoices ?? 0 }}</h3>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="text-center">
                                            <h6 class="text-muted">Total Amount</h6>
                                            <h3 class="text-success">${{ number_format($totalAmount ?? 0, 2) }}</h3>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="text-center">
                                            <h6 class="text-muted">Amount Paid</h6>
                                            <h3 class="text-info">${{ number_format($amountPaid ?? 0, 2) }}</h3>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="text-center">
                                            <h6 class="text-muted">Balance Due</h6>
                                            <h3 class="{{ ($balanceDue ?? 0) > 0 ? 'text-danger' : 'text-success' }}">
                                                ${{ number_format($balanceDue ?? 0, 2) }}
                                            </h3>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Recent Invoices -->
                        @if(isset($recentInvoices) && count($recentInvoices) > 0)
                            <div class="card mt-4">
                                <div class="card-header">
                                    <h5><i class="fas fa-file-invoice"></i> Recent Invoices</h5>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Invoice #</th>
                                                    <th>Date</th>
                                                    <th>Due Date</th>
                                                    <th>Amount</th>
                                                    <th>Status</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($recentInvoices as $invoice)
                                                    <tr>
                                                        <td>
                                                            <strong>{{ $invoice->invoice_number }}</strong>
                                                        </td>
                                                        <td>{{ $invoice->invoice_date->format('M d, Y') }}</td>
                                                        <td>{{ $invoice->due_date->format('M d, Y') }}</td>
                                                        <td>${{ number_format($invoice->total, 2) }}</td>
                                                        <td>
                                                            @php
                                                                $statusClass = [
                                                                    'draft' => 'secondary',
                                                                    'sent' => 'info',
                                                                    'paid' => 'success',
                                                                    'overdue' => 'danger'
                                                                ][$invoice->status] ?? 'secondary';
                                                            @endphp
                                                            <span class="badge bg-{{ $statusClass }}">{{ ucfirst($invoice->status) }}</span>
                                                        </td>
                                                        <td>
                                                            <a href="{{ route('invoices.show', $invoice) }}" 
                                                               class="btn btn-sm btn-outline-primary" title="View">
                                                                <i class="fas fa-eye"></i>
                                                            </a>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="text-center">
                                        <a href="{{ route('invoices.index', ['customer' => $customer->id]) }}" 
                                           class="btn btn-outline-primary">
                                            View All Invoices
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- Recent Payments -->
                        @if(isset($recentPayments) && count($recentPayments) > 0)
                            <div class="card mt-4">
                                <div class="card-header">
                                    <h5><i class="fas fa-money-bill"></i> Recent Payments</h5>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Date</th>
                                                    <th>Amount</th>
                                                    <th>Method</th>
                                                    <th>Invoice</th>
                                                    <th>Status</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($recentPayments as $payment)
                                                    <tr>
                                                        <td>{{ $payment->payment_date->format('M d, Y') }}</td>
                                                        <td class="text-success">${{ number_format($payment->amount, 2) }}</td>
                                                        <td>{{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }}</td>
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
                                                            @php
                                                                $paymentStatusClass = [
                                                                    'completed' => 'success',
                                                                    'pending' => 'warning',
                                                                    'failed' => 'danger'
                                                                ][$payment->status] ?? 'secondary';
                                                            @endphp
                                                            <span class="badge bg-{{ $paymentStatusClass }}">{{ ucfirst($payment->status) }}</span>
                                                        </td>
                                                        <td>
                                                            <a href="{{ route('payments.show', $payment) }}" 
                                                               class="btn btn-sm btn-outline-primary" title="View">
                                                                <i class="fas fa-eye"></i>
                                                            </a>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="text-center">
                                        <a href="{{ route('payments.index', ['customer' => $customer->id]) }}" 
                                           class="btn btn-outline-primary">
                                            View All Payments
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>

                    <div class="col-md-4">
                        <!-- Quick Actions -->
                        <div class="card">
                            <div class="card-header">
                                <h5><i class="fas fa-bolt"></i> Quick Actions</h5>
                            </div>
                            <div class="card-body">
                                <div class="d-grid gap-2">
                                    <a href="{{ route('invoices.create', ['customer_id' => $customer->id]) }}" 
                                       class="btn btn-primary">
                                        <i class="fas fa-file-invoice"></i> Create Invoice
                                    </a>
                                    <a href="{{ route('payments.create', ['customer_id' => $customer->id]) }}" 
                                       class="btn btn-success">
                                        <i class="fas fa-money-bill"></i> Record Payment
                                    </a>
                                    <a href="mailto:{{ $customer->email }}" class="btn btn-info">
                                        <i class="fas fa-envelope"></i> Send Email
                                    </a>
                                    <a href="{{ route('customers.edit', $customer) }}" class="btn btn-warning">
                                        <i class="fas fa-edit"></i> Edit Customer
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Customer Details -->
                        <div class="card mt-4">
                            <div class="card-header">
                                <h5><i class="fas fa-user-cog"></i> Customer Details</h5>
                            </div>
                            <div class="card-body">
                                @if($customer->tax_number)
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>Tax Number:</span>
                                        <span>{{ $customer->tax_number }}</span>
                                    </div>
                                @endif

                                @if($customer->credit_limit > 0)
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>Credit Limit:</span>
                                        <span>${{ number_format($customer->credit_limit, 2) }}</span>
                                    </div>
                                @endif

                                @if($customer->payment_terms)
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>Payment Terms:</span>
                                        <span>
                                            @php
                                                $terms = [
                                                    'net_15' => 'Net 15',
                                                    'net_30' => 'Net 30',
                                                    'net_45' => 'Net 45',
                                                    'net_60' => 'Net 60',
                                                    'due_on_receipt' => 'Due on Receipt',
                                                    'cod' => 'Cash on Delivery'
                                                ];
                                            @endphp
                                            {{ $terms[$customer->payment_terms] ?? $customer->payment_terms }}
                                        </span>
                                    </div>
                                @endif

                                @if($customer->is_tax_exempt)
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>Tax Status:</span>
                                        <span class="badge bg-success">Tax Exempt</span>
                                    </div>
                                @endif

                                <div class="d-flex justify-content-between">
                                    <span>Customer Since:</span>
                                    <span>{{ $customer->created_at->format('M d, Y') }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Account Activity -->
                        <div class="card mt-4">
                            <div class="card-header">
                                <h5><i class="fas fa-chart-line"></i> Account Activity</h5>
                            </div>
                            <div class="card-body">
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Last Invoice:</span>
                                    <span>
                                        @if(isset($lastInvoice))
                                            {{ $lastInvoice->invoice_date->format('M d, Y') }}
                                        @else
                                            Never
                                        @endif
                                    </span>
                                </div>
                                
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Last Payment:</span>
                                    <span>
                                        @if(isset($lastPayment))
                                            {{ $lastPayment->payment_date->format('M d, Y') }}
                                        @else
                                            Never
                                        @endif
                                    </span>
                                </div>
                                
                                <div class="d-flex justify-content-between">
                                    <span>Outstanding Invoices:</span>
                                    <span class="{{ ($outstandingInvoices ?? 0) > 0 ? 'text-danger' : 'text-success' }}">
                                        {{ $outstandingInvoices ?? 0 }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Notes -->
                        @if($customer->notes)
                            <div class="card mt-4">
                                <div class="card-header">
                                    <h5><i class="fas fa-sticky-note"></i> Notes</h5>
                                </div>
                                <div class="card-body">
                                    <p class="text-muted">{{ $customer->notes }}</p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="alert alert-danger">
            <h4>Customer not found</h4>
            <p>The requested customer could not be found.</p>
            <a href="{{ route('customers.index') }}" class="btn btn-primary">Back to Customers</a>
        </div>
    @endif
</div>
@endsection