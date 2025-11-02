@extends('layouts.app')

@section('title', 'Invoices')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1><i class="fas fa-file-invoice"></i> Invoices</h1>
                <a href="{{ route('invoices.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Create Invoice
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
                    <form method="GET" action="{{ route('invoices.index') }}" class="row g-3">
                        <div class="col-md-3">
                            <label for="status" class="form-label">Status</label>
                            <select name="status" id="status" class="form-select">
                                <option value="">All Statuses</option>
                                <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                                <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                                <option value="sent" {{ request('status') == 'sent' ? 'selected' : '' }}>Sent</option>
                                <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Paid</option>
                                <option value="overdue" {{ request('status') == 'overdue' ? 'selected' : '' }}>Overdue</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="customer" class="form-label">Customer</label>
                            <select name="customer" id="customer" class="form-select">
                                <option value="">All Customers</option>
                                @foreach($customers ?? [] as $customer)
                                    <option value="{{ $customer->id }}" {{ request('customer') == $customer->id ? 'selected' : '' }}>
                                        {{ $customer->name }}
                                    </option>
                                @endforeach
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
                            <a href="{{ route('invoices.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times"></i> Clear
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Invoices Table -->
            <div class="card">
                <div class="card-body">
                    @if(isset($invoices) && count($invoices) > 0)
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>Invoice #</th>
                                        <th>Customer</th>
                                        <th>Date</th>
                                        <th>Due Date</th>
                                        <th>Amount</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($invoices as $invoice)
                                        <tr>
                                            <td>
                                                <strong>{{ $invoice->invoice_number }}</strong>
                                            </td>
                                            <td>
                                                <a href="{{ route('customers.show', $invoice->customer) }}" class="text-decoration-none">
                                                    {{ $invoice->customer->name ?? 'N/A' }}
                                                </a>
                                            </td>
                                            <td>{{ $invoice->issue_date ? $invoice->issue_date->format('M d, Y') : 'N/A' }}</td>
                                            <td>
                                                @if($invoice->due_date)
                                                    <span class="{{ $invoice->due_date->isPast() && $invoice->status != 'paid' ? 'text-danger' : '' }}">
                                                        {{ $invoice->due_date->format('M d, Y') }}
                                                    </span>
                                                @else
                                                    N/A
                                                @endif
                                            </td>
                                            <td>
                                                <strong>${{ number_format($invoice->total_amount, 2) }}</strong>
                                            </td>
                                            <td>
                                                @php
                                                    $statusClass = [
                                                        'draft' => 'secondary',
                                                        'approved' => 'primary',
                                                        'sent' => 'info',
                                                        'paid' => 'success',
                                                        'overdue' => 'danger'
                                                    ][$invoice->status] ?? 'secondary';
                                                @endphp
                                                <span class="badge bg-{{ $statusClass }}">{{ ucfirst($invoice->status) }}</span>
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('invoices.show', $invoice) }}" 
                                                       class="btn btn-sm btn-outline-primary" title="View">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('invoices.edit', $invoice) }}" 
                                                       class="btn btn-sm btn-outline-warning" title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    @if($invoice->status == 'sent')
                                                        <a href="{{ route('invoices.send', $invoice) }}" 
                                                           class="btn btn-sm btn-outline-info" title="Send">
                                                            <i class="fas fa-paper-plane"></i>
                                                        </a>
                                                    @endif
                                                    @if($invoice->status == 'draft')
                                                        <form method="POST" action="{{ route('invoices.approve', $invoice) }}" style="display: inline;" onsubmit="return confirm('Are you sure you want to approve this invoice?')">
                                                            @csrf
                                                            @method('POST')
                                                            <button type="submit" class="btn btn-sm btn-outline-success" title="Approve">
                                                                <i class="fas fa-check"></i>
                                                            </button>
                                                        </form>
                                                    @endif
                                                    <form method="POST" action="{{ route('invoices.destroy', $invoice) }}" 
                                                          style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this invoice?')">
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
                        @if(method_exists($invoices, 'links'))
                            <div class="d-flex justify-content-center">
                                {{ $invoices->links() }}
                            </div>
                        @endif
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-file-invoice fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No invoices found</h5>
                            <p class="text-muted">Create your first invoice to get started.</p>
                            <a href="{{ route('invoices.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Create Invoice
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection