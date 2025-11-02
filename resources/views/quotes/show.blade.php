@extends('layouts.app')

@section('title', 'Quote #' . $quote->quote_number)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1><i class="fas fa-file-invoice-dollar"></i> Quote #{{ $quote->quote_number }}</h1>
                <div class="btn-group">
                    <a href="{{ route('quotes.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Quotes
                    </a>
                    @if($quote->status === 'draft')
                        <a href="{{ route('quotes.edit', $quote) }}" class="btn btn-primary">
                            <i class="fas fa-edit"></i> Edit Quote
                        </a>
                        <form method="POST" action="{{ route('quotes.send', $quote) }}" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-success" 
                                    onclick="return confirm('Send this quote to the customer?')">
                                <i class="fas fa-paper-plane"></i> Send Quote
                            </button>
                        </form>
                    @endif
                    @if($quote->status === 'sent' && !$quote->is_expired)
                        <form method="POST" action="{{ route('quotes.accept', $quote) }}" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-success" 
                                    onclick="return confirm('Accept this quote?')">
                                <i class="fas fa-check"></i> Accept Quote
                            </button>
                        </form>
                        <form method="POST" action="{{ route('quotes.reject', $quote) }}" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-danger" 
                                    onclick="return confirm('Reject this quote?')">
                                <i class="fas fa-times"></i> Reject Quote
                            </button>
                        </form>
                    @endif
                    @if($quote->status === 'accepted')
                        <a href="{{ route('quotes.convert-to-invoice', $quote) }}" class="btn btn-success">
                            <i class="fas fa-file-invoice"></i> Convert to Invoice
                        </a>
                    @endif
                </div>
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

            <div class="row">
                <!-- Quote Details -->
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-header">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">Quote Details</h5>
                                @php
                                    $statusClass = [
                                        'draft' => 'secondary',
                                        'sent' => 'info',
                                        'accepted' => 'success',
                                        'rejected' => 'danger',
                                        'expired' => 'warning'
                                    ][$quote->status] ?? 'primary';
                                @endphp
                                <span class="badge bg-{{ $statusClass }} fs-6">
                                    {{ ucfirst($quote->status) }}
                                </span>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <h6 class="text-muted">Customer Information</h6>
                                    <p class="mb-1"><strong>{{ $quote->customer->name }}</strong></p>
                                    <p class="mb-1">{{ $quote->customer->email }}</p>
                                    @if($quote->customer->phone)
                                        <p class="mb-1">{{ $quote->customer->phone }}</p>
                                    @endif
                                    @if($quote->customer->address)
                                        <p class="mb-0">{{ $quote->customer->address }}</p>
                                    @endif
                                </div>
                                <div class="col-md-6">
                                    <h6 class="text-muted">Quote Information</h6>
                                    <p class="mb-1"><strong>Quote Number:</strong> {{ $quote->quote_number }}</p>
                                    <p class="mb-1"><strong>Quote Date:</strong> {{ $quote->quote_date->format('M d, Y') }}</p>
                                    <p class="mb-1"><strong>Valid Until:</strong> 
                                        <span class="{{ $quote->is_expired ? 'text-danger' : '' }}">
                                            {{ $quote->valid_until->format('M d, Y') }}
                                        </span>
                                    </p>
                                    <p class="mb-0">
                                        <strong>Created By:</strong> {{ $quote->creator->name ?? 'N/A' }}
                                    </p>
                                </div>
                            </div>

                            @if($quote->is_expired)
                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-triangle"></i> This quote has expired and is no longer valid.
                                </div>
                            @endif

                            <!-- Quote Items -->
                            <h6 class="mb-3">Quote Items</h6>
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Description</th>
                                            <th class="text-center">Qty</th>
                                            <th class="text-end">Unit Price</th>
                                            <th class="text-center">Tax %</th>
                                            <th class="text-end">Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($quote->items as $item)
                                            <tr>
                                                <td>{{ $item->description }}</td>
                                                <td class="text-center">{{ number_format($item->quantity, 2) }}</td>
                                                <td class="text-end">${{ number_format($item->unit_price, 2) }}</td>
                                                <td class="text-center">{{ number_format($item->tax_rate, 2) }}%</td>
                                                <td class="text-end">${{ number_format($item->total_amount, 2) }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            @if($quote->terms)
                                <div class="mt-4">
                                    <h6 class="text-muted">Terms & Conditions</h6>
                                    <div class="border rounded p-3 bg-light">
                                        {!! nl2br(e($quote->terms)) !!}
                                    </div>
                                </div>
                            @endif

                            @if($quote->notes)
                                <div class="mt-3">
                                    <h6 class="text-muted">Notes</h6>
                                    <div class="border rounded p-3 bg-light">
                                        {!! nl2br(e($quote->notes)) !!}
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Summary Sidebar -->
                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Quote Summary</h5>
                        </div>
                        <div class="card-body">
                            <div class="d-flex justify-content-between mb-2">
                                <span>Subtotal:</span>
                                <strong>${{ number_format($quote->subtotal, 2) }}</strong>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Tax:</span>
                                <strong>${{ number_format($quote->tax_amount, 2) }}</strong>
                            </div>
                            @if($quote->discount_amount > 0)
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Discount:</span>
                                    <strong>-${{ number_format($quote->discount_amount, 2) }}</strong>
                                </div>
                            @endif
                            <hr>
                            <div class="d-flex justify-content-between">
                                <strong>Total:</strong>
                                <strong class="text-primary fs-5">${{ number_format($quote->total_amount, 2) }}</strong>
                            </div>

                            @if($quote->status === 'accepted')
                                <div class="alert alert-success mt-3">
                                    <i class="fas fa-check-circle"></i> This quote has been accepted and can be converted to an invoice.
                                </div>
                            @endif

                            @if($quote->status === 'sent' && $quote->is_expired)
                                <div class="alert alert-warning mt-3">
                                    <i class="fas fa-clock"></i> This quote has expired.
                                </div>
                            @endif

                            <div class="d-grid gap-2 mt-4">
                                <button class="btn btn-outline-primary" onclick="window.print()">
                                    <i class="fas fa-print"></i> Print Quote
                                </button>
                                <button class="btn btn-outline-secondary" onclick="navigator.clipboard.writeText('{{ $quote->quote_number }}')">
                                    <i class="fas fa-copy"></i> Copy Quote Number
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="card mt-3">
                        <div class="card-header">
                            <h6 class="mb-0">Quick Actions</h6>
                        </div>
                        <div class="card-body">
                            <div class="d-grid gap-2">
                                <a href="{{ route('customers.show', $quote->customer) }}" class="btn btn-outline-info btn-sm">
                                    <i class="fas fa-user"></i> View Customer
                                </a>
                                @if($quote->status === 'accepted')
                                    <a href="{{ route('invoices.create') }}?quote_id={{ $quote->id }}" class="btn btn-outline-success btn-sm">
                                        <i class="fas fa-file-invoice"></i> Create Invoice from Quote
                                    </a>
                                @endif
                                <form method="POST" action="{{ route('quotes.destroy', $quote) }}" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger btn-sm w-100"
                                            onclick="return confirm('Are you sure you want to delete this quote?')">
                                        <i class="fas fa-trash"></i> Delete Quote
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
@media print {
    .btn, .card-header, .alert { display: none !important; }
    .card { border: none !important; box-shadow: none !important; }
    .container-fluid { padding: 0 !important; }
}
</style>
@endsection
