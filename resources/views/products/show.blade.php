@extends('layouts.app')

@section('title', $product->name)

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-1">{{ $product->name }}</h1>
                    <p class="text-muted mb-0">SKU: {{ $product->sku }}</p>
                </div>
                <div class="d-flex gap-2">
                    @can('update', $product)
                        <a href="{{ route('products.edit', $product) }}" class="btn btn-primary">
                            <i class="fas fa-edit"></i> Edit Product
                        </a>
                    @endcan
                    <a href="{{ route('products.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Products
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Main Content -->
        <div class="col-lg-8">
            <!-- Product Overview -->
            <div class="card shadow-sm mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-info-circle"></i> Product Overview</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <dl class="row">
                                <dt class="col-sm-4">Name:</dt>
                                <dd class="col-sm-8">{{ $product->name }}</dd>
                                
                                <dt class="col-sm-4">SKU:</dt>
                                <dd class="col-sm-8">{{ $product->sku }}</dd>
                                
                                <dt class="col-sm-4">Category:</dt>
                                <dd class="col-sm-8">
                                    @if($product->category)
                                        {{ $product->category->name }}
                                    @else
                                        <span class="text-muted">No category</span>
                                    @endif
                                </dd>
                                
                                <dt class="col-sm-4">Unit:</dt>
                                <dd class="col-sm-8">{{ $product->unit ?? 'N/A' }}</dd>
                                
                                <dt class="col-sm-4">Barcode:</dt>
                                <dd class="col-sm-8">{{ $product->barcode ?? 'N/A' }}</dd>
                            </dl>
                        </div>
                        <div class="col-md-6">
                            <dl class="row">
                                <dt class="col-sm-4">Price:</dt>
                                <dd class="col-sm-8">
                                    <span class="fw-bold text-success">${{ number_format($product->price, 2) }}</span>
                                </dd>
                                
                                <dt class="col-sm-4">Cost:</dt>
                                <dd class="col-sm-8">
                                    @if($product->cost_price)
                                        ${{ number_format($product->cost_price, 2) }}
                                    @else
                                        <span class="text-muted">Not set</span>
                                    @endif
                                </dd>
                                
                                <dt class="col-sm-4">Taxable:</dt>
                                <dd class="col-sm-8">
                                    @if($product->taxable)
                                        <span class="badge bg-success">Yes</span>
                                        @if($product->tax_rate > 0)
                                            ({{ $product->tax_rate }}%)
                                        @endif
                                    @else
                                        <span class="badge bg-secondary">No</span>
                                    @endif
                                </dd>
                                
                                <dt class="col-sm-4">Status:</dt>
                                <dd class="col-sm-8">
                                    @if($product->is_active)
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-secondary">Inactive</span>
                                    @endif
                                    
                                    @if($product->is_featured)
                                        <span class="badge bg-info ms-1">Featured</span>
                                    @endif
                                </dd>
                                
                                <dt class="col-sm-4">Purchasing:</dt>
                                <dd class="col-sm-8">
                                    @if($product->allow_purchasing)
                                        <span class="badge bg-success">Allowed</span>
                                    @else
                                        <span class="badge bg-danger">Disabled</span>
                                    @endif
                                </dd>
                            </dl>
                        </div>
                    </div>
                    
                    @if($product->description)
                        <hr>
                        <h6>Description</h6>
                        <p class="text-muted">{{ $product->description }}</p>
                    @endif
                </div>
            </div>

            <!-- Inventory Status -->
            <div class="card shadow-sm mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-warehouse"></i> Inventory Status</h5>
                </div>
                <div class="card-body">
                    @if($product->track_inventory)
                        <div class="row mb-3">
                            <div class="col-md-3 text-center">
                                <div class="border rounded p-3">
                                    <h3 class="mb-1 {{ $product->stock_quantity <= $product->low_stock_alert ? 'text-danger' : 'text-primary' }}">
                                        {{ $product->stock_quantity }}
                                    </h3>
                                    <small class="text-muted">Current Stock</small>
                                </div>
                            </div>
                            <div class="col-md-3 text-center">
                                <div class="border rounded p-3">
                                    <h3 class="mb-1 text-warning">{{ $product->low_stock_alert }}</h3>
                                    <small class="text-muted">Low Stock Alert</small>
                                </div>
                            </div>
                            <div class="col-md-3 text-center">
                                <div class="border rounded p-3">
                                    <h3 class="mb-1 text-info">{{ $product->reorder_point }}</h3>
                                    <small class="text-muted">Reorder Point</small>
                                </div>
                            </div>
                            <div class="col-md-3 text-center">
                                <div class="border rounded p-3">
                                    <h3 class="mb-1 text-success">{{ $product->max_stock_level }}</h3>
                                    <small class="text-muted">Max Level</small>
                                </div>
                            </div>
                        </div>

                        <!-- Stock Level Indicator -->
                        <div class="mb-3">
                            <label class="form-label">Stock Level Status</label>
                            <div class="progress" style="height: 25px;">
                                @php
                                    $maxLevel = max($product->max_stock_level, $product->stock_quantity);
                                    $currentPercentage = ($product->stock_quantity / $maxLevel) * 100;
                                    $reorderPercentage = ($product->reorder_point / $maxLevel) * 100;
                                    $lowStockPercentage = ($product->low_stock_alert / $maxLevel) * 100;
                                @endphp
                                
                                <div class="progress-bar bg-danger" style="width: {{ $lowStockPercentage }}%"></div>
                                <div class="progress-bar bg-warning" style="width: {{ $reorderPercentage - $lowStockPercentage }}%"></div>
                                <div class="progress-bar bg-success" style="width: {{ 100 - $reorderPercentage }}%"></div>
                            </div>
                            <div class="d-flex justify-content-between mt-1">
                                <small class="text-danger">Low: {{ $product->low_stock_alert }}</small>
                                <small class="text-warning">Reorder: {{ $product->reorder_point }}</small>
                                <small class="text-success">Max: {{ $product->max_stock_level }}</small>
                            </div>
                        </div>

                        <!-- Stock Alerts -->
                        @if($product->stock_quantity <= $product->low_stock_alert)
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle"></i>
                                <strong>Low Stock Alert:</strong> Current stock ({{ $product->stock_quantity }}) is below the minimum threshold ({{ $product->low_stock_alert }}).
                            </div>
                        @elseif($product->stock_quantity <= $product->reorder_point)
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i>
                                <strong>Reorder Warning:</strong> Stock level ({{ $product->stock_quantity }}) has reached or is below the reorder point ({{ $product->reorder_point }}).
                            </div>
                        @else
                            <div class="alert alert-success">
                                <i class="fas fa-check-circle"></i>
                                <strong>Stock Level OK:</strong> Current stock ({{ $product->stock_quantity }}) is above reorder point.
                            </div>
                        @endif

                        @if($product->location)
                            <p><strong>Storage Location:</strong> {{ $product->location }}</p>
                        @endif
                    @else
                        <div class="alert alert-secondary">
                            <i class="fas fa-info-circle"></i>
                            <strong>Inventory tracking is disabled</strong> for this product.
                        </div>
                    @endif
                    
                    <p class="text-muted mb-0">
                        <small>Last updated: {{ $product->updated_at->format('M d, Y H:i') }}</small>
                    </p>
                </div>
            </div>

            <!-- Sales History -->
            <div class="card shadow-sm">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="fas fa-chart-line"></i> Sales History</h5>
                        <div>
                            <span class="badge bg-primary">{{ $product->invoiceItems->count() }} Transactions</span>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @if($product->invoiceItems->count() > 0)
                        @php
                            $monthlySales = $product->invoiceItems()
                                ->whereHas('invoice', function($query) {
                                    $query->whereYear('created_at', date('Y'));
                                })
                                ->selectRaw('MONTH(created_at) as month, SUM(quantity) as total_qty, SUM(total) as total_amount')
                                ->groupBy('month')
                                ->orderBy('month')
                                ->get();
                            
                            $totalSold = $product->invoiceItems->sum('quantity');
                            $totalRevenue = $product->invoiceItems->sum('total');
                        @endphp
                        
                        <div class="row mb-3">
                            <div class="col-md-4 text-center">
                                <div class="border rounded p-3">
                                    <h3 class="mb-1 text-primary">{{ $totalSold }}</h3>
                                    <small class="text-muted">Total Units Sold</small>
                                </div>
                            </div>
                            <div class="col-md-4 text-center">
                                <div class="border rounded p-3">
                                    <h3 class="mb-1 text-success">${{ number_format($totalRevenue, 2) }}</h3>
                                    <small class="text-muted">Total Revenue</small>
                                </div>
                            </div>
                            <div class="col-md-4 text-center">
                                <div class="border rounded p-3">
                                    <h3 class="mb-1 text-info">${{ $totalSold > 0 ? number_format($totalRevenue / $totalSold, 2) : '0.00' }}</h3>
                                    <small class="text-muted">Average Sale Price</small>
                                </div>
                            </div>
                        </div>

                        @if($monthlySales->count() > 0)
                            <h6>This Year's Monthly Sales</h6>
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Month</th>
                                            <th>Quantity Sold</th>
                                            <th>Revenue</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($monthlySales as $sale)
                                            <tr>
                                                <td>{{ DateTime::createFromFormat('!m', $sale->month)->format('F') }}</td>
                                                <td>{{ $sale->total_qty }}</td>
                                                <td>${{ number_format($sale->total_amount, 2) }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif

                        <div class="mt-3">
                            <h6>Recent Transactions</h6>
                            @php
                                $recentTransactions = $product->invoiceItems()
                                    ->with(['invoice.customer', 'invoice'])
                                    ->latest()
                                    ->limit(5)
                                    ->get();
                            @endphp
                            
                            @if($recentTransactions->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>Date</th>
                                                <th>Invoice #</th>
                                                <th>Customer</th>
                                                <th>Quantity</th>
                                                <th>Amount</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($recentTransactions as $item)
                                                <tr>
                                                    <td>{{ $item->invoice->created_at->format('M d, Y') }}</td>
                                                    <td>
                                                        <a href="{{ route('invoices.show', $item->invoice) }}" class="text-decoration-none">
                                                            {{ $item->invoice->invoice_number }}
                                                        </a>
                                                    </td>
                                                    <td>{{ $item->invoice->customer->name }}</td>
                                                    <td>{{ $item->quantity }}</td>
                                                    <td>${{ number_format($item->total, 2) }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <p class="text-muted">No recent transactions found.</p>
                            @endif
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-chart-line fa-3x text-muted mb-3"></i>
                            <h6 class="text-muted">No Sales History</h6>
                            <p class="text-muted">This product hasn't been sold yet.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Product Image -->
            @if($product->image)
                <div class="card shadow-sm mb-4">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-image"></i> Product Image</h5>
                    </div>
                    <div class="card-body text-center">
                        <img src="{{ asset('storage/' . $product->image) }}" 
                             alt="{{ $product->name }}" 
                             class="img-fluid rounded" 
                             style="max-height: 300px;">
                    </div>
                </div>
            @endif

            <!-- Quick Actions -->
            <div class="card shadow-sm mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-bolt"></i> Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        @can('create', App\Models\Invoice::class)
                            <a href="{{ route('invoices.create') }}?product_id={{ $product->id }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-file-invoice"></i> Create Invoice
                            </a>
                        @endcan
                        
                        @can('update', $product)
                            <button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#adjustStockModal">
                                <i class="fas fa-edit"></i> Adjust Stock
                            </button>
                        @endcan
                        
                        @can('view', App\Models\Report::class)
                            <a href="{{ route('reports.index') }}?product_id={{ $product->id }}" class="btn btn-outline-info btn-sm">
                                <i class="fas fa-chart-bar"></i> View Reports
                            </a>
                        @endcan
                        
                        <a href="{{ route('products.index') }}?category_id={{ $product->category_id }}" class="btn btn-outline-secondary btn-sm">
                            <i class="fas fa-tags"></i> View Category
                        </a>
                    </div>
                </div>
            </div>

            <!-- Product Performance -->
            <div class="card shadow-sm mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-trophy"></i> Performance</h5>
                </div>
                <div class="card-body">
                    @if($product->invoiceItems->count() > 0)
                        @php
                            $profitMargin = $product->cost_price ? 
                                (($product->price - $product->cost_price) / $product->price * 100) : 0;
                        @endphp
                        
                        <div class="row text-center mb-3">
                            <div class="col-6">
                                <div class="border-end">
                                    <h4 class="text-success">{{ number_format($profitMargin, 1) }}%</h4>
                                    <small class="text-muted">Profit Margin</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <h4 class="text-info">{{ $product->invoiceItems->count() }}</h4>
                                <small class="text-muted">Orders</small>
                            </div>
                        </div>
                        
                        @if($product->cost_price)
                            <p class="mb-2">
                                <strong>Revenue per Unit:</strong> 
                                <span class="text-success">${{ number_format($product->price - $product->cost_price, 2) }}</span>
                            </p>
                        @endif
                        
                        <p class="mb-0">
                            <strong>Avg. Monthly Sales:</strong> 
                            {{ number_format($product->invoiceItems->count() / 12, 1) }} units
                        </p>
                    @else
                        <div class="text-center py-3">
                            <i class="fas fa-chart-bar fa-2x text-muted mb-2"></i>
                            <p class="text-muted mb-0">No performance data available</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- System Information -->
            <div class="card shadow-sm">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-info"></i> System Information</h5>
                </div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-6">Created:</dt>
                        <dd class="col-6">{{ $product->created_at->format('M d, Y') }}</dd>
                        
                        <dt class="col-6">Updated:</dt>
                        <dd class="col-6">{{ $product->updated_at->format('M d, Y H:i') }}</dd>
                        
                        <dt class="col-6">ID:</dt>
                        <dd class="col-6">#{{ $product->id }}</dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Adjust Stock Modal -->
@can('update', $product)
    <div class="modal fade" id="adjustStockModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Adjust Stock - {{ $product->name }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('products.adjust-stock', $product) }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="current_stock" class="form-label">Current Stock</label>
                            <input type="number" class="form-control" id="current_stock" 
                                   value="{{ $product->stock_quantity }}" readonly>
                        </div>
                        
                        <div class="mb-3">
                            <label for="adjustment_type" class="form-label">Adjustment Type</label>
                            <select class="form-select" id="adjustment_type" name="adjustment_type" required>
                                <option value="add">Add Stock</option>
                                <option value="subtract">Remove Stock</option>
                                <option value="set">Set Exact Amount</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="quantity" class="form-label" id="quantity-label">Quantity to Add</label>
                            <input type="number" class="form-control" id="quantity" name="quantity" 
                                   min="1" step="1" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="reason" class="form-label">Reason</label>
                            <textarea class="form-control" id="reason" name="reason" rows="3" 
                                      placeholder="Enter reason for stock adjustment..." required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Adjust Stock</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const adjustmentType = document.getElementById('adjustment_type');
        const quantityLabel = document.getElementById('quantity-label');
        
        adjustmentType.addEventListener('change', function() {
            const type = this.value;
            switch(type) {
                case 'add':
                    quantityLabel.textContent = 'Quantity to Add';
                    document.getElementById('quantity').min = '1';
                    break;
                case 'subtract':
                    quantityLabel.textContent = 'Quantity to Remove';
                    document.getElementById('quantity').min = '1';
                    break;
                case 'set':
                    quantityLabel.textContent = 'New Stock Level';
                    document.getElementById('quantity').min = '0';
                    break;
            }
        });
    });
    </script>
@endcan

@endsection