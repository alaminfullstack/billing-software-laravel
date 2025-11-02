@extends('layouts.app')

@section('title', 'Inventory Transactions')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1><i class="fas fa-boxes"></i> Inventory Transactions</h1>
                <a href="{{ route('inventory-transactions.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Record Transaction
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
                                    <i class="fas fa-boxes fa-2x"></i>
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
                                    <h6 class="card-title">Purchases</h6>
                                    <h3 class="mb-0">{{ $stats['purchases'] }}</h3>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-shopping-cart fa-2x"></i>
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
                                    <h6 class="card-title">Sales</h6>
                                    <h3 class="mb-0">{{ $stats['sales'] }}</h3>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-cash-register fa-2x"></i>
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
                                    <h6 class="card-title">Adjustments</h6>
                                    <h3 class="mb-0">{{ $stats['adjustments'] }}</h3>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-tools fa-2x"></i>
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
                                    <h6 class="card-title">Net Qty</h6>
                                    <h3 class="mb-0">{{ number_format($stats['totalQuantity']) }}</h3>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-balance-scale fa-2x"></i>
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
                                    <h6 class="card-title">Total Cost</h6>
                                    <h3 class="mb-0">${{ number_format($stats['totalCost'], 0) }}</h3>
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
                        <div class="col-md-2">
                            <label for="type" class="form-label">Type</label>
                            <select class="form-select" id="type" name="type">
                                <option value="">All Types</option>
                                <option value="purchase" {{ request('type') == 'purchase' ? 'selected' : '' }}>Purchase</option>
                                <option value="sale" {{ request('type') == 'sale' ? 'selected' : '' }}>Sale</option>
                                <option value="adjustment" {{ request('type') == 'adjustment' ? 'selected' : '' }}>Adjustment</option>
                                <option value="return" {{ request('type') == 'return' ? 'selected' : '' }}>Return</option>
                                <option value="transfer" {{ request('type') == 'transfer' ? 'selected' : '' }}>Transfer</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="product_id" class="form-label">Product</label>
                            <select class="form-select" id="product_id" name="product_id">
                                <option value="">All Products</option>
                                @foreach($products as $product)
                                    <option value="{{ $product->id }}" {{ request('product_id') == $product->id ? 'selected' : '' }}>
                                        {{ $product->name }} ({{ $product->sku }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="date_from" class="form-label">From Date</label>
                            <input type="date" class="form-control" id="date_from" name="date_from" 
                                   value="{{ request('date_from') }}">
                        </div>
                        <div class="col-md-2">
                            <label for="date_to" class="form-label">To Date</label>
                            <input type="date" class="form-control" id="date_to" name="date_to" 
                                   value="{{ request('date_to') }}">
                        </div>
                        <div class="col-md-3">
                            <label for="search" class="form-label">Search</label>
                            <input type="text" class="form-control" id="search" name="search" 
                                   placeholder="Product name, SKU, or notes..." 
                                   value="{{ request('search') }}">
                        </div>
                        <div class="col-md-1 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </form>
                    <div class="mt-2">
                        <a href="{{ route('inventory-transactions.index') }}" class="btn btn-outline-secondary btn-sm">
                            <i class="fas fa-times"></i> Clear Filters
                        </a>
                    </div>
                </div>
            </div>

            <!-- Transactions Table -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Transaction History ({{ $transactions->total() }})</h5>
                </div>
                <div class="card-body">
                    @if($transactions->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Date & Time</th>
                                        <th>Product</th>
                                        <th>Type</th>
                                        <th class="text-center">Quantity</th>
                                        <th>Stock Change</th>
                                        <th>Unit Cost</th>
                                        <th>Total Cost</th>
                                        <th>Reference</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($transactions as $transaction)
                                        <tr>
                                            <td>
                                                <div>
                                                    <strong>{{ $transaction->created_at->format('M d, Y') }}</strong>
                                                    <br><small class="text-muted">{{ $transaction->created_at->format('H:i') }}</small>
                                                </div>
                                            </td>
                                            <td>
                                                <div>
                                                    <strong>{{ $transaction->product->name }}</strong>
                                                    <br><small class="text-muted">{{ $transaction->product->sku }}</small>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge bg-{{ $transaction->type_color }}">
                                                    {{ ucfirst($transaction->type) }}
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                <strong>{{ $transaction->formatted_quantity }}</strong>
                                            </td>
                                            <td>
                                                <div>
                                                    <small class="text-muted">{{ $transaction->previous_stock }}</small>
                                                    <i class="fas fa-arrow-right mx-1"></i>
                                                    <strong>{{ $transaction->new_stock }}</strong>
                                                </div>
                                            </td>
                                            <td>
                                                @if($transaction->unit_cost)
                                                    ${{ number_format($transaction->unit_cost, 2) }}
                                                @else
                                                    <span class="text-muted">N/A</span>
                                                @endif
                                            </td>
                                            <td>
                                                <strong>${{ number_format($transaction->formatted_total_cost, 2) }}</strong>
                                            </td>
                                            <td>
                                                @if($transaction->reference_type)
                                                    <small>
                                                        <strong>{{ ucfirst($transaction->reference_type) }}</strong>
                                                        @if($transaction->reference_id)
                                                            #{{ $transaction->reference_id }}
                                                        @endif
                                                    </small>
                                                @else
                                                    <span class="text-muted">Manual</span>
                                                @endif
                                                @if($transaction->notes)
                                                    <br><small class="text-muted">{{ Str::limit($transaction->notes, 30) }}</small>
                                                @endif
                                            </td>
                                            <td>
                                                <a href="{{ route('inventory-transactions.show', $transaction) }}" 
                                                   class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-eye"></i> View
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        {{ $transactions->links() }}
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-boxes fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No inventory transactions found</h5>
                            <p class="text-muted">Start recording inventory transactions to track stock movements.</p>
                            <a href="{{ route('inventory-transactions.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Record Transaction
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
