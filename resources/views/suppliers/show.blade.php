@extends('layouts.app')

@section('title', 'Supplier: ' . $supplier->name)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1><i class="fas fa-truck"></i> {{ $supplier->name }}</h1>
                <div class="btn-group">
                    <a href="{{ route('suppliers.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Suppliers
                    </a>
                    <a href="{{ route('suppliers.edit', $supplier) }}" class="btn btn-primary">
                        <i class="fas fa-edit"></i> Edit Supplier
                    </a>
                </div>
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="row">
                <!-- Supplier Details -->
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-header">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">Supplier Information</h5>
                                <span class="badge bg-{{ $supplier->status === 'active' ? 'success' : ($supplier->status === 'inactive' ? 'secondary' : 'danger') }} fs-6">
                                    {{ ucfirst($supplier->status) }}
                                </span>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <h6 class="text-muted">Contact Information</h6>
                                    <dl class="row">
                                        <dt class="col-sm-4">Email:</dt>
                                        <dd class="col-sm-8">
                                            <a href="mailto:{{ $supplier->email }}">{{ $supplier->email }}</a>
                                        </dd>
                                        
                                        @if($supplier->phone)
                                        <dt class="col-sm-4">Phone:</dt>
                                        <dd class="col-sm-8">
                                            <a href="tel:{{ $supplier->phone }}">{{ $supplier->phone }}</a>
                                        </dd>
                                        @endif
                                        
                                        @if($supplier->website)
                                        <dt class="col-sm-4">Website:</dt>
                                        <dd class="col-sm-8">
                                            <a href="{{ $supplier->website }}" target="_blank" class="text-decoration-none">
                                                {{ $supplier->website }}
                                            </a>
                                        </dd>
                                        @endif
                                    </dl>
                                </div>
                                <div class="col-md-6">
                                    <h6 class="text-muted">Business Details</h6>
                                    <dl class="row">
                                        @if($supplier->tax_number)
                                        <dt class="col-sm-5">Tax Number:</dt>
                                        <dd class="col-sm-7"><code>{{ $supplier->tax_number }}</code></dd>
                                        @endif
                                        
                                        <dt class="col-sm-5">Business Type:</dt>
                                        <dd class="col-sm-7">
                                            @if($supplier->tax_number)
                                                <span class="badge bg-info">Business</span>
                                            @else
                                                <span class="badge bg-secondary">Individual</span>
                                            @endif
                                        </dd>
                                        
                                        <dt class="col-sm-5">Created:</dt>
                                        <dd class="col-sm-7">{{ $supplier->created_at->format('M d, Y') }}</dd>
                                        
                                        <dt class="col-sm-5">Updated:</dt>
                                        <dd class="col-sm-7">{{ $supplier->updated_at->format('M d, Y') }}</dd>
                                    </dl>
                                </div>
                            </div>

                            @if($supplier->address || $supplier->city || $supplier->state || $supplier->country)
                                <div class="mt-4">
                                    <h6 class="text-muted">Address</h6>
                                    <address class="border rounded p-3 bg-light">
                                        @if($supplier->address)
                                            {{ $supplier->address }}<br>
                                        @endif
                                        @if($supplier->city || $supplier->state)
                                            {{ $supplier->city ?? '' }}{{ $supplier->city && $supplier->state ? ', ' : '' }}{{ $supplier->state ?? '' }}<br>
                                        @endif
                                        @if($supplier->zip_code)
                                            {{ $supplier->zip_code }}<br>
                                        @endif
                                        @if($supplier->country)
                                            {{ $supplier->country }}
                                        @endif
                                    </address>
                                </div>
                            @endif

                            @if($supplier->notes)
                                <div class="mt-4">
                                    <h6 class="text-muted">Notes</h6>
                                    <div class="border rounded p-3 bg-light">
                                        {!! nl2br(e($supplier->notes)) !!}
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Expense History -->
                    @if($supplier->expenses->count() > 0)
                        <div class="card mt-4">
                            <div class="card-header">
                                <h5 class="mb-0">Recent Expenses ({{ $supplier->expenses->count() }})</h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Date</th>
                                                <th>Description</th>
                                                <th>Category</th>
                                                <th>Amount</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($supplier->expenses->sortByDesc('created_at')->take(10) as $expense)
                                                <tr>
                                                    <td>{{ $expense->expense_date->format('M d, Y') }}</td>
                                                    <td>
                                                        <a href="{{ route('expenses.show', $expense) }}" class="text-decoration-none">
                                                            {{ Str::limit($expense->description, 50) }}
                                                        </a>
                                                    </td>
                                                    <td>{{ $expense->category->name ?? 'N/A' }}</td>
                                                    <td><strong>${{ number_format($expense->amount, 2) }}</strong></td>
                                                    <td>
                                                        <span class="badge bg-{{ $expense->status === 'approved' ? 'success' : ($expense->status === 'pending' ? 'warning' : 'danger') }}">
                                                            {{ ucfirst($expense->status) }}
                                                        </span>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                    @if($supplier->expenses->count() > 10)
                                        <small class="text-muted">
                                            Showing 10 of {{ $supplier->expenses->count() }} expenses
                                        </small>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Summary Sidebar -->
                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Summary</h5>
                        </div>
                        <div class="card-body">
                            <div class="row text-center">
                                <div class="col-6">
                                    <h4 class="text-primary">{{ $expenseStats['total'] }}</h4>
                                    <small class="text-muted">Total Expenses</small>
                                </div>
                                <div class="col-6">
                                    <h4 class="text-success">${{ number_format($expenseStats['totalAmount'], 2) }}</h4>
                                    <small class="text-muted">Total Amount</small>
                                </div>
                            </div>
                            
                            <hr>
                            
                            <div class="row text-center">
                                <div class="col-4">
                                    <h6 class="text-warning">{{ $expenseStats['pending'] }}</h6>
                                    <small class="text-muted">Pending</small>
                                </div>
                                <div class="col-4">
                                    <h6 class="text-success">{{ $expenseStats['approved'] }}</h6>
                                    <small class="text-muted">Approved</small>
                                </div>
                                <div class="col-4">
                                    <h6 class="text-info">{{ $expenseStats['paid'] }}</h6>
                                    <small class="text-muted">Paid</small>
                                </div>
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
                                <a href="{{ route('expenses.create') }}?supplier_id={{ $supplier->id }}" class="btn btn-outline-success btn-sm">
                                    <i class="fas fa-plus"></i> Add Expense
                                </a>
                                
                                <form method="POST" action="{{ route('suppliers.toggle', $supplier) }}">
                                    @csrf
                                    <button type="submit" 
                                            class="btn btn-{{ $supplier->status === 'active' ? 'warning' : 'success' }} btn-sm w-100"
                                            onclick="return confirm('{{ $supplier->status === 'active' ? 'Deactivate' : 'Activate' }} this supplier?')">
                                        <i class="fas fa-{{ $supplier->status === 'active' ? 'pause' : 'play' }}"></i>
                                        {{ $supplier->status === 'active' ? 'Deactivate Supplier' : 'Activate Supplier' }}
                                    </button>
                                </form>
                                
                                <form method="POST" action="{{ route('suppliers.destroy', $supplier) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger btn-sm w-100"
                                            onclick="return confirm('Delete this supplier? This action cannot be undone.')">
                                        <i class="fas fa-trash"></i> Delete Supplier
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Business Information -->
                    @if($supplier->tax_number)
                        <div class="card mt-3">
                            <div class="card-header">
                                <h6 class="mb-0">Business Information</h6>
                            </div>
                            <div class="card-body">
                                <div class="alert alert-info">
                                    <i class="fas fa-building"></i>
                                    <strong>Business Supplier</strong><br>
                                    This supplier has a tax number on file.
                                </div>
                                
                                <dl class="row mb-0">
                                    <dt class="col-6">Tax Number:</dt>
                                    <dd class="col-6"><code>{{ $supplier->tax_number }}</code></dd>
                                </dl>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
