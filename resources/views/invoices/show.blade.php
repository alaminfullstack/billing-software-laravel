@extends('layouts.app')

@section('title', 'Invoice Details')

@section('content')
<div class="container-fluid">
    @if(isset($invoice))
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1><i class="fas fa-file-invoice"></i> Invoice #{{ $invoice->invoice_number }}</h1>
                    <div>
                        <a href="{{ route('invoices.index') }}" class="btn btn-outline-secondary me-2">
                            <i class="fas fa-arrow-left"></i> Back to Invoices
                        </a>
                        <div class="btn-group" role="group">
                            <a href="{{ route('invoices.edit', $invoice) }}" class="btn btn-warning">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            @if($invoice->status == 'draft')
                                <a href="{{ route('invoices.approve', $invoice) }}" 
                                   class="btn btn-success"
                                   onclick="return confirm('Are you sure you want to approve this invoice?')">
                                    <i class="fas fa-check"></i> Approve
                                </a>
                            @endif
                            @if($invoice->status == 'sent')
                                <a href="{{ route('invoices.send', $invoice) }}" class="btn btn-info">
                                    <i class="fas fa-paper-plane"></i> Send
                                </a>
                            @endif
                            <a href="{{ route('invoices.download.pdf', $invoice) }}" class="btn btn-secondary">
                                <i class="fas fa-download"></i> PDF
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Invoice Details -->
                <div class="row">
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-header">
                                <h5><i class="fas fa-info-circle"></i> Invoice Information</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <h6 class="text-muted">Invoice Number</h6>
                                        <p class="fw-bold">{{ $invoice->invoice_number }}</p>
                                    </div>
                                    <div class="col-md-6">
                                        <h6 class="text-muted">Status</h6>
                                        <p>
                                            @php
                                                $statusClass = [
                                                    'draft' => 'secondary',
                                                    'sent' => 'info', 
                                                    'paid' => 'success',
                                                    'overdue' => 'danger'
                                                ][$invoice->status] ?? 'secondary';
                                            @endphp
                                            <span class="badge bg-{{ $statusClass }}">{{ ucfirst($invoice->status) }}</span>
                                        </p>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <h6 class="text-muted">Invoice Date</h6>
                                        <p>{{ $invoice->invoice_date->format('M d, Y') }}</p>
                                    </div>
                                    <div class="col-md-6">
                                        <h6 class="text-muted">Due Date</h6>
                                        <p class="{{ $invoice->due_date->isPast() && $invoice->status != 'paid' ? 'text-danger fw-bold' : '' }}">
                                            {{ $invoice->due_date->format('M d, Y') }}
                                            @if($invoice->due_date->isPast() && $invoice->status != 'paid')
                                                <small class="text-danger">(Overdue)</small>
                                            @endif
                                        </p>
                                    </div>
                                </div>

                                @if($invoice->notes)
                                    <div class="row">
                                        <div class="col-12">
                                            <h6 class="text-muted">Notes</h6>
                                            <p>{{ $invoice->notes }}</p>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Customer Information -->
                        <div class="card mt-4">
                            <div class="card-header">
                                <h5><i class="fas fa-user"></i> Customer Information</h5>
                            </div>
                            <div class="card-body">
                                @if($invoice->customer)
                                    <div class="row">
                                        <div class="col-md-6">
                                            <h6 class="text-muted">Customer Name</h6>
                                            <p class="fw-bold">{{ $invoice->customer->name }}</p>
                                        </div>
                                        <div class="col-md-6">
                                            <h6 class="text-muted">Email</h6>
                                            <p>{{ $invoice->customer->email }}</p>
                                        </div>
                                    </div>
                                    @if($invoice->customer->phone)
                                        <div class="row">
                                            <div class="col-md-6">
                                                <h6 class="text-muted">Phone</h6>
                                                <p>{{ $invoice->customer->phone }}</p>
                                            </div>
                                            <div class="col-md-6">
                                                <h6 class="text-muted">Address</h6>
                                                <p>{{ $invoice->customer->address ?? 'N/A' }}</p>
                                            </div>
                                        </div>
                                    @endif
                                    <div class="mt-3">
                                        <a href="{{ route('customers.show', $invoice->customer) }}" class="btn btn-outline-primary btn-sm">
                                            <i class="fas fa-eye"></i> View Customer Details
                                        </a>
                                    </div>
                                @else
                                    <p class="text-muted">Customer information not available</p>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <!-- Invoice Summary -->
                        <div class="card">
                            <div class="card-header">
                                <h5><i class="fas fa-calculator"></i> Invoice Summary</h5>
                            </div>
                            <div class="card-body">
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Subtotal:</span>
                                    <strong>${{ number_format($invoice->subtotal, 2) }}</strong>
                                </div>
                                
                                @if($invoice->tax_amount > 0)
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>Tax:</span>
                                        <strong>${{ number_format($invoice->tax_amount, 2) }}</strong>
                                    </div>
                                @endif
                                
                                @if($invoice->discount_amount > 0)
                                    <div class="d-flex justify-content-between mb-2 text-success">
                                        <span>Discount:</span>
                                        <strong>-${{ number_format($invoice->discount_amount, 2) }}</strong>
                                    </div>
                                @endif
                                
                                <hr>
                                
                                <div class="d-flex justify-content-between">
                                    <h6>Total:</h6>
                                    <h6 class="fw-bold text-primary">${{ number_format($invoice->total, 2) }}</h6>
                                </div>

                                <div class="d-flex justify-content-between mt-2">
                                    <span>Paid:</span>
                                    <strong class="text-success">${{ number_format($invoice->paid_amount ?? 0, 2) }}</strong>
                                </div>
                                
                                <div class="d-flex justify-content-between">
                                    <span>Balance:</span>
                                    <strong class="{{ ($invoice->total - ($invoice->paid_amount ?? 0)) > 0 ? 'text-danger' : 'text-success' }}">
                                        ${{ number_format($invoice->total - ($invoice->paid_amount ?? 0), 2) }}
                                    </strong>
                                </div>
                            </div>
                        </div>

                        <!-- Quick Actions -->
                        <div class="card mt-4">
                            <div class="card-header">
                                <h5><i class="fas fa-bolt"></i> Quick Actions</h5>
                            </div>
                            <div class="card-body">
                                @if($invoice->status == 'sent' || $invoice->status == 'overdue')
                                    <a href="{{ route('payments.create', ['invoice_id' => $invoice->id]) }}" 
                                       class="btn btn-success w-100 mb-2">
                                        <i class="fas fa-dollar-sign"></i> Record Payment
                                    </a>
                                @endif
                                
                                @if($invoice->status == 'draft')
                                    <a href="{{ route('invoices.edit', $invoice) }}" 
                                       class="btn btn-warning w-100 mb-2">
                                        <i class="fas fa-edit"></i> Edit Invoice
                                    </a>
                                @endif

                                <div class="btn-group w-100">
                                    <a href="{{ route('invoices.download.pdf', $invoice) }}" 
                                       class="btn btn-outline-primary">
                                        <i class="fas fa-download"></i> Download PDF
                                    </a>
                                    @if($invoice->status == 'sent')
                                        <a href="{{ route('invoices.send', $invoice) }}" 
                                           class="btn btn-outline-info">
                                            <i class="fas fa-paper-plane"></i> Resend
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Invoice Items -->
                <div class="card mt-4">
                    <div class="card-header">
                        <h5><i class="fas fa-list"></i> Invoice Items</h5>
                    </div>
                    <div class="card-body">
                        @if(isset($invoice->items) && count($invoice->items) > 0)
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Description</th>
                                            <th>Quantity</th>
                                            <th>Unit Price</th>
                                            <th>Tax Rate</th>
                                            <th class="text-end">Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($invoice->items as $item)
                                            <tr>
                                                <td>
                                                    <strong>{{ $item->description }}</strong>
                                                    @if($item->product)
                                                        <br><small class="text-muted">Product: {{ $item->product->name }}</small>
                                                    @endif
                                                </td>
                                                <td>{{ $item->quantity }}</td>
                                                <td>${{ number_format($item->unit_price, 2) }}</td>
                                                <td>{{ $item->tax_rate ?? 0 }}%</td>
                                                <td class="text-end">
                                                    <strong>${{ number_format($item->quantity * $item->unit_price * (1 + ($item->tax_rate ?? 0) / 100), 2) }}</strong>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p class="text-muted">No items found for this invoice.</p>
                        @endif
                    </div>
                </div>

                <!-- Payments History -->
                @if(isset($invoice->payments) && count($invoice->payments) > 0)
                    <div class="card mt-4">
                        <div class="card-header">
                            <h5><i class="fas fa-money-bill"></i> Payments History</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Amount</th>
                                            <th>Method</th>
                                            <th>Reference</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($invoice->payments as $payment)
                                            <tr>
                                                <td>{{ $payment->payment_date->format('M d, Y') }}</td>
                                                <td class="text-success">${{ number_format($payment->amount, 2) }}</td>
                                                <td>{{ ucfirst($payment->payment_method) }}</td>
                                                <td>{{ $payment->reference_number ?? 'N/A' }}</td>
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
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    @else
        <div class="alert alert-danger">
            <h4>Invoice not found</h4>
            <p>The requested invoice could not be found.</p>
            <a href="{{ route('invoices.index') }}" class="btn btn-primary">Back to Invoices</a>
        </div>
    @endif
</div>
@endsection