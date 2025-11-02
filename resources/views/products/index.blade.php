@extends('layouts.app')

@section('title', 'Products & Services')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1><i class="fas fa-box"></i> Products & Services</h1>
                <div class="btn-group" role="group">
                    <a href="{{ route('products.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Add Product
                    </a>
                    <a href="{{ route('products.create', ['type' => 'service']) }}" class="btn btn-success">
                        <i class="fas fa-plus"></i> Add Service
                    </a>
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

            <!-- Product Statistics -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card bg-primary text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="card-title">Total Products</h6>
                                    <h3 class="mb-0">{{ $totalProducts ?? 0 }}</h3>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-box fa-2x"></i>
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
                                    <h6 class="card-title">Services</h6>
                                    <h3 class="mb-0">{{ $totalServices ?? 0 }}</h3>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-concierge-bell fa-2x"></i>
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
                                    <h6 class="card-title">Low Stock</h6>
                                    <h3 class="mb-0">{{ $lowStockCount ?? 0 }}</h3>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-exclamation-triangle fa-2x"></i>
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
                                    <h6 class="card-title">Total Value</h6>
                                    <h3 class="mb-0">${{ number_format($totalInventoryValue ?? 0, 2) }}</h3>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-dollar-sign fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filters and Search -->
            <div class="card mb-4">
                <div class="card-body">
                    <form method="GET" action="{{ route('products.index') }}" class="row g-3">
                        <div class="col-md-3">
                            <label for="search" class="form-label">Search</label>
                            <input type="text" name="search" id="search" class="form-control" 
                                   value="{{ request('search') }}" placeholder="Search products/services...">
                        </div>
                        <div class="col-md-2">
                            <label for="type" class="form-label">Type</label>
                            <select name="type" id="type" class="form-select">
                                <option value="">All Types</option>
                                <option value="product" {{ request('type') == 'product' ? 'selected' : '' }}>Products</option>
                                <option value="service" {{ request('type') == 'service' ? 'selected' : '' }}>Services</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="category" class="form-label">Category</label>
                            <select name="category" id="category" class="form-select">
                                <option value="">All Categories</option>
                                @foreach($categories ?? [] as $category)
                                    <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="status" class="form-label">Status</label>
                            <select name="status" id="status" class="form-select">
                                <option value="">All Status</option>
                                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="stock_status" class="form-label">Stock Status</label>
                            <select name="stock_status" id="stock_status" class="form-select">
                                <option value="">All Stock Levels</option>
                                <option value="in_stock" {{ request('stock_status') == 'in_stock' ? 'selected' : '' }}>In Stock</option>
                                <option value="low_stock" {{ request('stock_status') == 'low_stock' ? 'selected' : '' }}>Low Stock</option>
                                <option value="out_of_stock" {{ request('stock_status') == 'out_of_stock' ? 'selected' : '' }}>Out of Stock</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-outline-primary">
                                <i class="fas fa-filter"></i> Filter
                            </button>
                            <a href="{{ route('products.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times"></i> Clear
                            </a>
                            <button type="button" class="btn btn-outline-info" data-bs-toggle="modal" data-bs-target="#exportModal">
                                <i class="fas fa-download"></i> Export
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Products Table -->
            <div class="card">
                <div class="card-body">
                    @if(isset($products) && count($products) > 0)
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>
                                            <input type="checkbox" id="select-all">
                                        </th>
                                        <th>Name</th>
                                        <th>Type</th>
                                        <th>Category</th>
                                        <th>Price</th>
                                        <th>Stock</th>
                                        <th>Status</th>
                                        <th>Last Updated</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($products as $product)
                                        <tr class="{{ $product->isLowStock() ? 'table-warning' : '' }}">
                                            <td>
                                                <input type="checkbox" class="product-checkbox" value="{{ $product->id }}">
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="me-3">
                                                        @if($product->image)
                                                            <img src="{{ $product->image }}" alt="{{ $product->name }}" 
                                                                 class="rounded" width="40" height="40" style="object-fit: cover;">
                                                        @else
                                                            <div class="bg-light rounded d-flex align-items-center justify-content-center" 
                                                                 style="width: 40px; height: 40px;">
                                                                <i class="fas {{ $product->type == 'product' ? 'fa-box' : 'fa-concierge-bell' }} text-muted"></i>
                                                            </div>
                                                        @endif
                                                    </div>
                                                    <div>
                                                        <strong>{{ $product->name }}</strong>
                                                        @if($product->description)
                                                            <br><small class="text-muted">{{ Str::limit($product->description, 50) }}</small>
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge bg-{{ $product->type == 'product' ? 'primary' : 'success' }}">
                                                    {{ ucfirst($product->type) }}
                                                </span>
                                            </td>
                                            <td>{{ $product->category->name ?? 'No Category' }}</td>
                                            <td>
                                                <strong>${{ number_format($product->price, 2) }}</strong>
                                                @if($product->cost > 0)
                                                    <br><small class="text-muted">Cost: ${{ number_format($product->cost, 2) }}</small>
                                                @endif
                                            </td>
                                            <td>
                                                @if($product->track_stock)
                                                    <span class="{{ $product->stock_quantity <= 0 ? 'text-danger' : ($product->isLowStock() ? 'text-warning' : 'text-success') }}">
                                                        {{ $product->stock_quantity ?? 0 }}
                                                        @if($product->stock_quantity <= 0)
                                                            <i class="fas fa-exclamation-circle"></i>
                                                        @elseif($product->isLowStock())
                                                            <i class="fas fa-exclamation-triangle"></i>
                                                        @endif
                                                    </span>
                                                @else
                                                    <span class="text-muted">N/A</span>
                                                @endif
                                            </td>
                                            <td>
                                                @php
                                                    $statusClass = $product->status == 'active' ? 'success' : 'secondary';
                                                @endphp
                                                <span class="badge bg-{{ $statusClass }}">{{ ucfirst($product->status) }}</span>
                                            </td>
                                            <td>
                                                <small>{{ $product->updated_at->format('M d, Y') }}</small>
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('products.show', $product) }}" 
                                                       class="btn btn-sm btn-outline-primary" title="View">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('products.edit', $product) }}" 
                                                       class="btn btn-sm btn-outline-warning" title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <button type="button" class="btn btn-sm btn-outline-info" 
                                                            onclick="duplicateProduct({{ $product->id }})" title="Duplicate">
                                                        <i class="fas fa-copy"></i>
                                                    </button>
                                                    <form method="POST" action="{{ route('products.destroy', $product) }}" 
                                                          style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this {{ $product->type }}?')">
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

                        <!-- Bulk Actions -->
                        <div class="row mt-3" id="bulk-actions" style="display: none;">
                            <div class="col-md-6">
                                <div class="btn-group">
                                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="bulkAction('activate')">
                                        <i class="fas fa-check"></i> Activate
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="bulkAction('deactivate')">
                                        <i class="fas fa-times"></i> Deactivate
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-success" onclick="bulkAction('export')">
                                        <i class="fas fa-download"></i> Export Selected
                                    </button>
                                </div>
                            </div>
                            <div class="col-md-6 text-end">
                                <button type="button" class="btn btn-sm btn-outline-danger" onclick="bulkAction('delete')">
                                    <i class="fas fa-trash"></i> Delete Selected
                                </button>
                            </div>
                        </div>

                        <!-- Pagination -->
                        @if(method_exists($products, 'links'))
                            <div class="d-flex justify-content-center">
                                {{ $products->links() }}
                            </div>
                        @endif
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-box fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No products or services found</h5>
                            <p class="text-muted">Add your first product or service to get started.</p>
                            <div class="btn-group">
                                <a href="{{ route('products.create') }}" class="btn btn-primary">
                                    <i class="fas fa-plus"></i> Add Product
                                </a>
                                <a href="{{ route('products.create', ['type' => 'service']) }}" class="btn btn-success">
                                    <i class="fas fa-plus"></i> Add Service
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Export Modal -->
<div class="modal fade" id="exportModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Export Products & Services</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="GET" action="{{ route('products.export') }}">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="export_format" class="form-label">Export Format</label>
                        <select name="format" id="export_format" class="form-select">
                            <option value="csv">CSV</option>
                            <option value="excel">Excel</option>
                            <option value="pdf">PDF</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="export_fields" class="form-label">Fields to Export</label>
                        <select name="fields[]" id="export_fields" class="form-select" multiple>
                            <option value="name" selected>Name</option>
                            <option value="type" selected>Type</option>
                            <option value="description">Description</option>
                            <option value="price" selected>Price</option>
                            <option value="cost">Cost</option>
                            <option value="stock">Stock</option>
                            <option value="category">Category</option>
                            <option value="status" selected>Status</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Export</button>
                </div>
            </form>
        </div>
    </div>
</div>

@section('scripts')
<script>
$(document).ready(function() {
    // Select all checkbox
    $('#select-all').change(function() {
        $('.product-checkbox').prop('checked', this.checked);
        toggleBulkActions();
    });

    // Individual checkboxes
    $('.product-checkbox').change(function() {
        toggleBulkActions();
    });

    function toggleBulkActions() {
        const checkedCount = $('.product-checkbox:checked').length;
        if (checkedCount > 0) {
            $('#bulk-actions').show();
        } else {
            $('#bulk-actions').hide();
        }
    }

    function bulkAction(action) {
        const selectedIds = $('.product-checkbox:checked').map(function() {
            return this.value;
        }).get();

        if (selectedIds.length === 0) {
            alert('Please select at least one item.');
            return;
        }

        let confirmMessage = '';
        switch(action) {
            case 'activate':
                confirmMessage = 'Are you sure you want to activate the selected items?';
                break;
            case 'deactivate':
                confirmMessage = 'Are you sure you want to deactivate the selected items?';
                break;
            case 'delete':
                confirmMessage = 'Are you sure you want to delete the selected items? This action cannot be undone.';
                break;
            case 'export':
                // Export functionality
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '/products/bulk-export';
                
                const csrfToken = document.createElement('input');
                csrfToken.type = 'hidden';
                csrfToken.name = '_token';
                csrfToken.value = document.querySelector('meta[name="csrf-token"]').content;
                form.appendChild(csrfToken);

                selectedIds.forEach(id => {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'ids[]';
                    input.value = id;
                    form.appendChild(input);
                });

                document.body.appendChild(form);
                form.submit();
                return;
        }

        if (confirm(confirmMessage)) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '/products/bulk-' + action;
            
            const csrfToken = document.createElement('input');
            csrfToken.type = 'hidden';
            csrfToken.name = '_token';
            csrfToken.value = document.querySelector('meta[name="csrf-token"]').content;
            form.appendChild(csrfToken);

            selectedIds.forEach(id => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'ids[]';
                input.value = id;
                form.appendChild(input);
            });

            document.body.appendChild(form);
            form.submit();
        }
    }

    function duplicateProduct(id) {
        if (confirm('Are you sure you want to duplicate this item?')) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '/products/' + id + '/duplicate';
            
            const csrfToken = document.createElement('input');
            csrfToken.type = 'hidden';
            csrfToken.name = '_token';
            csrfToken.value = document.querySelector('meta[name="csrf-token"]').content;
            form.appendChild(csrfToken);

            document.body.appendChild(form);
            form.submit();
        }
    }
});
</script>
@endsection
@endsection