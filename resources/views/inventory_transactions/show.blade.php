@extends('layouts.app')

@section('title', 'Inventory Transaction Details')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1><i class="fas fa-boxes"></i> Transaction Details</h1>
                <div class="btn-group">
                    <a href="{{ route('inventory-transactions.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Transactions
                    </a>
                    <a href="{{ route('inventory-transactions.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> New Transaction
                    </a>
                </div>
            </div>

            <div class="row">
                <!-- Transaction Details -->
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-header">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">Transaction Information</h5>
                                <span class="badge bg-{{ $transaction->type_color }} fs-6">
                                    {{ ucfirst($transaction->type) }}
                                </span>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <dl class="row">
                                        <dt class="col-sm-4">Transaction ID:</dt>
                                        <dd class="col-sm-8"><strong>#{{ $transaction->id }}</strong></dd>
                                        
                                        <dt class="col-sm-4">Type:</dt>
                                        <dd class="col-sm-8">
                                            <span class="badge bg-{{ $transaction->type_color }}">
                                                {{ ucfirst($transaction->type) }}
                                            </span>
                                        </dd>
                                        
                                        <dt class="col-sm-4">Product:</dt>
                                        <dd class="col-sm-8">
                                            <a href="{{ route('products.show', $transaction->product) }}" class="text-decoration-none">
                                                <strong>{{ $transaction->product->name }}</strong>
                                            </a>
                                            <br><small class="text-muted">{{ $transaction->product->sku }}</small>
                                        </dd>
                                        
                                        <dt class="col-sm-4">Date & Time:</dt>
                                        <dd class="col-sm-8">
                                            {{ $transaction->created_at->format('M d, Y') }}
                                            <br><small class="text-muted">{{ $transaction->created_at->format('H:i:s') }}</small>
                                        </dd>
                                    </dl>
                                </div>
                                <div class="col-md-6">
                                    <dl class="row">
                                        <dt class="col-sm-5">Quantity:</dt>
                                        <dd class="col-sm-7">
                                            <strong>{{ $transaction->formatted_quantity }}</strong>
                                        </dd>
                                        
                                        <dt class="col-sm-5">Unit Cost:</dt>
                                        <dd class="col-sm-7">
                                            @if($transaction->unit_cost)
                                                ${{ number_format($transaction->unit_cost, 2) }}
                                            @else
                                                <span class="text-muted">N/A</span>
                                            @endif
                                        </dd>
                                        
                                        <dt class="col-sm-5">Total Cost:</dt>
                                        <dd class="col-sm-7">
                                            <strong class="text-success">${{ number_format($transaction->formatted_total_cost, 2) }}</strong>
                                        </dd>
                                        
                                        <dt class="col-sm-5">Recorded By:</dt>
                                        <dd class="col-sm-7">{{ $transaction->user->name ?? 'System' }}</dd>
                                    </dl>
                                </div>
                            </div>

                            <hr>

                            <!-- Stock Movement -->
                            <div class="row">
                                <div class="col-12">
                                    <h6 class="text-muted">Stock Movement</h6>
                                    <div class="row text-center">
                                        <div class="col-4">
                                            <div class="border rounded p-3">
                                                <h4 class="text-muted">{{ $transaction->previous_stock }}</h4>
                                                <small class="text-muted">Previous Stock</small>
                                            </div>
                                        </div>
                                        <div class="col-4 d-flex align-items-center justify-content-center">
                                            <div class="text-center">
                                                <i class="fas fa-arrow-right fa-2x text-primary"></i>
                                                <br>
                                                <small class="text-muted">{{ $transaction->formatted_quantity }}</small>
                                            </div>
                                        </div>
                                        <div class="col-4">
                                            <div class="border rounded p-3">
                                                <h4 class="text-primary">{{ $transaction->new_stock }}</h4>
                                                <small class="text-muted">New Stock</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            @if($transaction->reference_type)
                                <hr>
                                <div class="row">
                                    <div class="col-md-6">
                                        <h6 class="text-muted">Reference Information</h6>
                                        <dl class="row">
                                            <dt class="col-sm-5">Reference Type:</dt>
                                            <dd class="col-sm-7">{{ ucfirst($transaction->reference_type) }}</dd>
                                            
                                            @if($transaction->reference_id)
                                            <dt class="col-sm-5">Reference ID:</dt>
                                            <dd class="col-sm-7">#{{ $transaction->reference_id }}</dd>
                                            @endif
                                        </dl>
                                    </div>
                                </div>
                            @endif

                            @if($transaction->notes)
                                <hr>
                                <div class="row">
                                    <div class="col-12">
                                        <h6 class="text-muted">Notes</h6>
                                        <div class="border rounded p-3 bg-light">
                                            {!! nl2br(e($transaction->notes)) !!}
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Product Information -->
                    <div class="card mt-4">
                        <div class="card-header">
                            <h5 class="mb-0">Related Product Information</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <dl class="row">
                                        <dt class="col-sm-4">Product Name:</dt>
                                        <dd class="col-sm-8"><strong>{{ $transaction->product->name }}</strong></dd>
                                        
                                        <dt class="col-sm-4">SKU:</dt>
                                        <dd class="col-sm-8"><code>{{ $transaction->product->sku }}</code></dd>
                                        
                                        <dt class="col-sm-4">Category:</dt>
                                        <dd class="col-sm-8">{{ $transaction->product->category->name ?? 'N/A' }}</dd>
                                    </dl>
                                </div>
                                <div class="col-md-6">
                                    <dl class="row">
                                        <dt class="col-sm-5">Current Stock:</dt>
                                        <dd class="col-sm-7">
                                            <strong class="text-{{ $transaction->product->stock_quantity <= $transaction->product->low_stock_alert ? 'danger' : 'success' }}">
                                                {{ $transaction->product->stock_quantity }}
                                            </strong>
                                        </dd>
                                        
                                        <dt class="col-sm-5">Reorder Level:</dt>
                                        <dd class="col-sm-7">{{ $transaction->product->reorder_level }}</dd>
                                        
                                        <dt class="col-sm-5">Low Stock Alert:</dt>
                                        <dd class="col-sm-7">{{ $transaction->product->low_stock_alert }}</dd>
                                    </dl>
                                </div>
                            </div>

                            @if($transaction->product->stock_quantity <= $transaction->product->low_stock_alert)
                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-triangle"></i>
                                    <strong>Low Stock Alert:</strong> This product is below the reorder threshold.
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Actions Sidebar -->
                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Actions</h5>
                        </div>
                        <div class="card-body">
                            <div class="d-grid gap-2">
                                <a href="{{ route('products.show', $transaction->product) }}" class="btn btn-outline-info">
                                    <i class="fas fa-box"></i> View Product
                                </a>
                                
                                <a href="{{ route('inventory-transactions.create') }}" class="btn btn-outline-primary">
                                    <i class="fas fa-plus"></i> New Transaction
                                </a>
                                
                                @if($transaction->type === 'adjustment')
                                    <a href="{{ route('inventory-transactions.create') }}?product_id={{ $transaction->product_id }}" class="btn btn-outline-warning">
                                        <i class="fas fa-sync"></i> Create Adjustment
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Quick Stats -->
                    <div class="card mt-3">
                        <div class="card-header">
                            <h6 class="mb-0">Quick Stats</h6>
                        </div>
                        <div class="card-body">
                            <div class="row text-center">
                                <div class="col-6">
                                    <h4 class="text-primary">{{ $transaction->product->stock_quantity }}</h4>
                                    <small class="text-muted">Current Stock</small>
                                </div>
                                <div class="col-6">
                                    <h4 class="text-info">{{ $transaction->product->reorder_level }}</h4>
                                    <small class="text-muted">Reorder Level</small>
                                </div>
                            </div>
                            
                            @if($transaction->product->track_inventory)
                                <hr>
                                <div class="text-center">
                                    @if($transaction->product->stock_quantity <= $transaction->product->low_stock_alert)
                                        <span class="badge bg-danger p-2">
                                            <i class="fas fa-exclamation-triangle"></i> Low Stock
                                        </span>
                                    @elseif($transaction->product->stock_quantity <= $transaction->product->reorder_level)
                                        <span class="badge bg-warning p-2">
                                            <i class="fas fa-exclamation"></i> Reorder Soon
                                        </span>
                                    @else
                                        <span class="badge bg-success p-2">
                                            <i class="fas fa-check-circle"></i> In Stock
                                        </span>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Transaction Type Info -->
                    <div class="card mt-3">
                        <div class="card-header">
                            <h6 class="mb-0">Transaction Type</h6>
                        </div>
                        <div class="card-body">
                            @php
                                $typeInfo = [
                                    'purchase' => ['icon' => 'shopping-cart', 'color' => 'success', 'desc' => 'Stock increase from purchases'],
                                    'sale' => ['icon' => 'cash-register', 'color' => 'danger', 'desc' => 'Stock decrease from sales'],
                                    'adjustment' => ['icon' => 'tools', 'color' => 'warning', 'desc' => 'Manual stock adjustments'],
                                    'return' => ['icon' => 'undo', 'color' => 'info', 'desc' => 'Returned items to stock'],
                                    'transfer' => ['icon' => 'exchange-alt', 'color' => 'secondary', 'desc' => 'Stock transfers between locations'],
                                ];
                                $info = $typeInfo[$transaction->type] ?? ['icon' => 'question', 'color' => 'primary', 'desc' => 'Unknown transaction type'];
                            @endphp
                            
                            <div class="text-center">
                                <i class="fas fa-{{ $info['icon'] }} fa-3x text-{{ $info['color'] }} mb-3"></i>
                                <h5 class="text-{{ $info['color'] }}">{{ ucfirst($transaction->type) }}</h5>
                                <p class="text-muted small">{{ $info['desc'] }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
