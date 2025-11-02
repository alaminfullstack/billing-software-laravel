@extends('layouts.app')

@section('title', 'Suppliers')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1><i class="fas fa-truck"></i> Suppliers</h1>
                <a href="{{ route('suppliers.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Add Supplier
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
                <div class="col-md-3">
                    <div class="card bg-primary text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="card-title">Total Suppliers</h6>
                                    <h3 class="mb-0">{{ $stats['total'] }}</h3>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-truck fa-2x"></i>
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
                                    <h6 class="card-title">Active</h6>
                                    <h3 class="mb-0">{{ $stats['active'] }}</h3>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-check-circle fa-2x"></i>
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
                                    <h6 class="card-title">Business</h6>
                                    <h3 class="mb-0">{{ $stats['business'] }}</h3>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-building fa-2x"></i>
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
                                    <h6 class="card-title">Total Expenses</h6>
                                    <h3 class="mb-0">${{ number_format($stats['totalExpenses'], 0) }}</h3>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-dollar-sign fa-2x"></i>
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
                                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                <option value="blocked" {{ request('status') == 'blocked' ? 'selected' : '' }}>Blocked</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="search" class="form-label">Search</label>
                            <input type="text" class="form-control" id="search" name="search" 
                                   placeholder="Name, email, phone, or tax number..." 
                                   value="{{ request('search') }}">
                        </div>
                        <div class="col-md-3 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary me-2">
                                <i class="fas fa-search"></i> Filter
                            </button>
                            <a href="{{ route('suppliers.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times"></i> Clear
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Suppliers Table -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Suppliers List ({{ $suppliers->total() }})</h5>
                </div>
                <div class="card-body">
                    @if($suppliers->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Supplier</th>
                                        <th>Contact</th>
                                        <th>Location</th>
                                        <th>Tax Number</th>
                                        <th>Status</th>
                                        <th>Total Expenses</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($suppliers as $supplier)
                                        <tr>
                                            <td>
                                                <div>
                                                    <strong>{{ $supplier->name }}</strong>
                                                    @if($supplier->tax_number)
                                                        <br><small class="text-muted">Tax: {{ $supplier->tax_number }}</small>
                                                    @endif
                                                </div>
                                            </td>
                                            <td>
                                                <div>
                                                    <small>{{ $supplier->email }}</small>
                                                    @if($supplier->phone)
                                                        <br><small class="text-muted">{{ $supplier->phone }}</small>
                                                    @endif
                                                </div>
                                            </td>
                                            <td>
                                                @if($supplier->city || $supplier->state)
                                                    <small>
                                                        {{ $supplier->city ?? '' }}{{ $supplier->city && $supplier->state ? ', ' : '' }}{{ $supplier->state ?? '' }}
                                                        @if($supplier->country)
                                                            <br>{{ $supplier->country }}
                                                        @endif
                                                    </small>
                                                @else
                                                    <span class="text-muted">Not specified</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($supplier->tax_number)
                                                    <code>{{ $supplier->tax_number }}</code>
                                                @else
                                                    <span class="text-muted">None</span>
                                                @endif
                                            </td>
                                            <td>
                                                @php
                                                    $statusClass = [
                                                        'active' => 'success',
                                                        'inactive' => 'secondary',
                                                        'blocked' => 'danger'
                                                    ][$supplier->status] ?? 'primary';
                                                @endphp
                                                <span class="badge bg-{{ $statusClass }}">
                                                    {{ ucfirst($supplier->status) }}
                                                </span>
                                            </td>
                                            <td>
                                                <div>
                                                    <strong>${{ number_format($supplier->total_expenses, 2) }}</strong>
                                                    <br><small class="text-muted">{{ $supplier->expense_count }} expenses</small>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('suppliers.show', $supplier) }}" 
                                                       class="btn btn-sm btn-outline-primary">
                                                        <i class="fas fa-eye"></i> View
                                                    </a>
                                                    <a href="{{ route('suppliers.edit', $supplier) }}" 
                                                       class="btn btn-sm btn-outline-secondary">
                                                        <i class="fas fa-edit"></i> Edit
                                                    </a>
                                                    <form method="POST" 
                                                          action="{{ route('suppliers.toggle', $supplier) }}" 
                                                          class="d-inline">
                                                        @csrf
                                                        <button type="submit" 
                                                                class="btn btn-sm btn-outline-{{ $supplier->status === 'active' ? 'warning' : 'success' }}"
                                                                onclick="return confirm('{{ $supplier->status === 'active' ? 'Deactivate' : 'Activate' }} this supplier?')">
                                                            <i class="fas fa-{{ $supplier->status === 'active' ? 'pause' : 'play' }}"></i>
                                                            {{ $supplier->status === 'active' ? 'Deactivate' : 'Activate' }}
                                                        </button>
                                                    </form>
                                                    <form method="POST" 
                                                          action="{{ route('suppliers.destroy', $supplier) }}" 
                                                          class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" 
                                                                class="btn btn-sm btn-outline-danger"
                                                                onclick="return confirm('Delete this supplier? This action cannot be undone.')">
                                                            <i class="fas fa-trash"></i> Delete
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        {{ $suppliers->links() }}
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-truck fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No suppliers found</h5>
                            <p class="text-muted">Add your first supplier to get started.</p>
                            <a href="{{ route('suppliers.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Add Supplier
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
