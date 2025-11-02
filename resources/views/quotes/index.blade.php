@extends('layouts.app')

@section('title', 'Quotes')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1><i class="fas fa-file-invoice-dollar"></i> Quotes</h1>
                <a href="{{ route('quotes.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Create Quote
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

            <!-- Statistics -->
            <div class="row mb-4">
                <div class="col-md-2">
                    <div class="card bg-primary text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="card-title">Total</h6>
                                    <h3 class="mb-0">{{ $stats['total'] }}</h3>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-file-invoice-dollar fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="card bg-secondary text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="card-title">Draft</h6>
                                    <h3 class="mb-0">{{ $stats['draft'] }}</h3>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-edit fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="card bg-info text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="card-title">Sent</h6>
                                    <h3 class="mb-0">{{ $stats['sent'] }}</h3>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-paper-plane fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="card bg-success text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="card-title">Accepted</h6>
                                    <h3 class="mb-0">{{ $stats['accepted'] }}</h3>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-check-circle fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="card bg-danger text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="card-title">Rejected</h6>
                                    <h3 class="mb-0">{{ $stats['rejected'] }}</h3>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-times-circle fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="card bg-warning text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="card-title">Expired</h6>
                                    <h3 class="mb-0">{{ $stats['expired'] }}</h3>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-clock fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filters -->
            <div class="card mb-4">
                <div class="card-body">
                    <form method="GET" class="row g-3">
                        <div class="col-md-3">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select" id="status" name="status">
                                <option value="">All Statuses</option>
                                <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                                <option value="sent" {{ request('status') == 'sent' ? 'selected' : '' }}>Sent</option>
                                <option value="accepted" {{ request('status') == 'accepted' ? 'selected' : '' }}>Accepted</option>
                                <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                                <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>Expired</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="search" class="form-label">Search</label>
                            <input type="text" class="form-control" id="search" name="search" 
                                   placeholder="Quote number or customer name..." 
                                   value="{{ request('search') }}">
                        </div>
                        <div class="col-md-3 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary me-2">
                                <i class="fas fa-search"></i> Filter
                            </button>
                            <a href="{{ route('quotes.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times"></i> Clear
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Quotes Table -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Quotes List ({{ $quotes->total() }})</h5>
                </div>
                <div class="card-body">
                    @if($quotes->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Quote #</th>
                                        <th>Customer</th>
                                        <th>Date</th>
                                        <th>Valid Until</th>
                                        <th>Amount</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($quotes as $quote)
                                        <tr>
                                            <td>
                                                <strong>{{ $quote->quote_number }}</strong>
                                            </td>
                                            <td>
                                                <a href="{{ route('customers.show', $quote->customer) }}" class="text-decoration-none">
                                                    {{ $quote->customer->name }}
                                                </a>
                                            </td>
                                            <td>{{ $quote->quote_date->format('M d, Y') }}</td>
                                            <td>
                                                <span class="{{ $quote->is_expired ? 'text-danger' : '' }}">
                                                    {{ $quote->valid_until->format('M d, Y') }}
                                                </span>
                                                @if($quote->is_expired)
                                                    <br><small class="text-danger">Expired</small>
                                                @endif
                                            </td>
                                            <td>
                                                <strong>${{ number_format($quote->total_amount, 2) }}</strong>
                                            </td>
                                            <td>
                                                @php
                                                    $statusClass = [
                                                        'draft' => 'secondary',
                                                        'sent' => 'info',
                                                        'accepted' => 'success',
                                                        'rejected' => 'danger',
                                                        'expired' => 'warning'
                                                    ][$quote->status] ?? 'primary';
                                                @endphp
                                                <span class="badge bg-{{ $statusClass }}">
                                                    {{ ucfirst($quote->status) }}
                                                </span>
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('quotes.show', $quote) }}" 
                                                       class="btn btn-sm btn-outline-primary">
                                                        <i class="fas fa-eye"></i> View
                                                    </a>
                                                    @if($quote->status === 'draft')
                                                        <a href="{{ route('quotes.edit', $quote) }}" 
                                                           class="btn btn-sm btn-outline-secondary">
                                                            <i class="fas fa-edit"></i> Edit
                                                        </a>
                                                    @endif
                                                    @if($quote->status === 'sent' && !$quote->is_expired)
                                                        <form method="POST" 
                                                              action="{{ route('quotes.accept', $quote) }}" 
                                                              class="d-inline">
                                                            @csrf
                                                            <button type="submit" class="btn btn-sm btn-outline-success"
                                                                    onclick="return confirm('Accept this quote?')">
                                                                <i class="fas fa-check"></i> Accept
                                                            </button>
                                                        </form>
                                                    @endif
                                                    @if($quote->status === 'accepted')
                                                        <a href="{{ route('quotes.convert-to-invoice', $quote) }}" 
                                                           class="btn btn-sm btn-outline-success">
                                                            <i class="fas fa-file-invoice"></i> Convert
                                                        </a>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        {{ $quotes->links() }}
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-file-invoice-dollar fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No quotes found</h5>
                            <p class="text-muted">Create your first quote to get started.</p>
                            <a href="{{ route('quotes.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Create Quote
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
