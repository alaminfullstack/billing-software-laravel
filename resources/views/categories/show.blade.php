@extends('layouts.app')

@section('title', $category->name)

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div class="d-flex align-items-center">
                    <div class="me-3" style="font-size: 2rem; color: {{ $category->color }};">
                        <i class="{{ $category->icon ?: 'fas fa-folder' }}"></i>
                    </div>
                    <div>
                        <h1 class="h3 mb-1">{{ $category->name }}</h1>
                        <p class="text-muted mb-0">
                            {{ ucfirst($category->type) }} Category | 
                            <span class="badge bg-{{ $category->is_active ? 'success' : 'secondary' }}">
                                {{ $category->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </p>
                    </div>
                </div>
                <div class="d-flex gap-2">
                    @can('update', $category)
                        <a href="{{ route('categories.edit', $category) }}" class="btn btn-primary">
                            <i class="fas fa-edit"></i> Edit Category
                        </a>
                    @endcan
                    <a href="{{ route('categories.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Categories
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Main Content -->
        <div class="col-lg-8">
            <!-- Category Overview -->
            <div class="card shadow-sm mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-info-circle"></i> Category Overview</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <dl class="row">
                                <dt class="col-sm-4">Name:</dt>
                                <dd class="col-sm-8">{{ $category->name }}</dd>
                                
                                <dt class="col-sm-4">Type:</dt>
                                <dd class="col-sm-8">
                                    <span class="badge bg-primary">{{ ucfirst($category->type) }}</span>
                                </dd>
                                
                                <dt class="col-sm-4">Parent:</dt>
                                <dd class="col-sm-8">
                                    @if($category->parent)
                                        <a href="{{ route('categories.show', $category->parent) }}" class="text-decoration-none">
                                            {{ $category->parent->name }}
                                        </a>
                                    @else
                                        <span class="text-muted">Top Level</span>
                                    @endif
                                </dd>
                                
                                <dt class="col-sm-4">Depth:</dt>
                                <dd class="col-sm-8">{{ $category->depth ?? 0 }} level(s)</dd>
                                
                                <dt class="col-sm-4">Color:</dt>
                                <dd class="col-sm-8">
                                    <div class="d-flex align-items-center">
                                        <div class="color-box me-2" style="width: 20px; height: 20px; background-color: {{ $category->color }}; border-radius: 3px;"></div>
                                        <code>{{ $category->color }}</code>
                                    </div>
                                </dd>
                            </dl>
                        </div>
                        <div class="col-md-6">
                            <dl class="row">
                                <dt class="col-sm-4">Tax Rate:</dt>
                                <dd class="col-sm-8">
                                    @if($category->default_tax_rate > 0)
                                        {{ number_format($category->default_tax_rate, 2) }}%
                                    @else
                                        <span class="text-muted">Not set</span>
                                    @endif
                                </dd>
                                
                                <dt class="col-sm-4">Taxable:</dt>
                                <dd class="col-sm-8">
                                    @if($category->is_taxable)
                                        <span class="badge bg-success">Yes</span>
                                    @else
                                        <span class="badge bg-secondary">No</span>
                                    @endif
                                </dd>
                                
                                <dt class="col-sm-4">Account Code:</dt>
                                <dd class="col-sm-8">{{ $category->account_code ?? 'N/A' }}</dd>
                                
                                <dt class="col-sm-4">Default:</dt>
                                <dd class="col-sm-8">
                                    @if($category->is_default)
                                        <span class="badge bg-info">Yes</span>
                                    @else
                                        <span class="badge bg-light text-dark">No</span>
                                    @endif
                                </dd>
                                
                                <dt class="col-sm-4">Subcategories:</dt>
                                <dd class="col-sm-8">
                                    {{ $category->children->count() }} 
                                    @if($category->children->count() > 0)
                                        <button class="btn btn-sm btn-outline-info ms-2" type="button" data-bs-toggle="collapse" data-bs-target="#subcategories">
                                            <i class="fas fa-chevron-down"></i>
                                        </button>
                                    @endif
                                </dd>
                            </dl>
                        </div>
                    </div>
                    
                    @if($category->description)
                        <hr>
                        <h6>Description</h6>
                        <p class="text-muted">{{ $category->description }}</p>
                    @endif
                </div>
            </div>

            <!-- Subcategories -->
            @if($category->children->count() > 0)
                <div class="card shadow-sm mb-4">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0"><i class="fas fa-sitemap"></i> Subcategories ({{ $category->children->count() }})</h5>
                            <button class="btn btn-sm btn-outline-primary" type="button" data-bs-toggle="collapse" data-bs-target="#subcategories">
                                <i class="fas fa-expand-alt"></i> Toggle
                            </button>
                        </div>
                    </div>
                    <div class="collapse" id="subcategories">
                        <div class="card-body">
                            <div class="row">
                                @foreach($category->children->sortBy('name') as $child)
                                    <div class="col-md-6 mb-3">
                                        <div class="card border-start border-4" style="border-color: {{ $child->color }} !important;">
                                            <div class="card-body">
                                                <div class="d-flex justify-content-between align-items-start">
                                                    <div class="d-flex align-items-center">
                                                        <div class="me-2" style="color: {{ $child->color }};">
                                                            <i class="{{ $child->icon ?: 'fas fa-folder' }}"></i>
                                                        </div>
                                                        <div>
                                                            <h6 class="mb-1">
                                                                <a href="{{ route('categories.show', $child) }}" class="text-decoration-none">
                                                                    {{ $child->name }}
                                                                </a>
                                                            </h6>
                                                            <small class="text-muted">{{ ucfirst($child->type) }}</small>
                                                        </div>
                                                    </div>
                                                    <div>
                                                        @if(!$child->is_active)
                                                            <span class="badge bg-secondary">Inactive</span>
                                                        @endif
                                                    </div>
                                                </div>
                                                @if($child->description)
                                                    <p class="text-muted small mb-0 mt-2">{{ Str::limit($child->description, 80) }}</p>
                                                @endif
                                                <div class="mt-2">
                                                    <small class="text-muted">
                                                        {{ $child->children->count() }} subcategories | 
                                                        {{ $child->products_count ?? 0 }} products
                                                    </small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Usage Statistics -->
            <div class="card shadow-sm">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-chart-bar"></i> Usage Statistics</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 text-center mb-3">
                            <div class="border rounded p-3">
                                <h3 class="mb-1 text-primary">{{ $category->products_count ?? 0 }}</h3>
                                <small class="text-muted">Products</small>
                            </div>
                        </div>
                        <div class="col-md-3 text-center mb-3">
                            <div class="border rounded p-3">
                                <h3 class="mb-1 text-success">{{ $category->expenses_count ?? 0 }}</h3>
                                <small class="text-muted">Expenses</small>
                            </div>
                        </div>
                        <div class="col-md-3 text-center mb-3">
                            <div class="border rounded p-3">
                                <h3 class="mb-1 text-info">{{ $category->children->count() }}</h3>
                                <small class="text-muted">Subcategories</small>
                            </div>
                        </div>
                        <div class="col-md-3 text-center mb-3">
                            <div class="border rounded p-3">
                                <h3 class="mb-1 text-warning">{{ $category->depth ?? 0 }}</h3>
                                <small class="text-muted">Depth Level</small>
                            </div>
                        </div>
                    </div>

                    @if($category->type === 'product')
                        @php
                            $categoryProducts = $category->products()
                                ->with(['invoiceItems' => function($query) {
                                    $query->whereYear('created_at', date('Y'));
                                }])
                                ->get();
                            
                            $totalRevenue = $categoryProducts->flatMap->invoiceItems->sum('total');
                            $totalQuantity = $categoryProducts->flatMap->invoiceItems->sum('quantity');
                        @endphp
                        
                        @if($totalRevenue > 0)
                            <hr>
                            <h6>This Year's Performance</h6>
                            <div class="row">
                                <div class="col-md-4 text-center">
                                    <h5 class="text-success">${{ number_format($totalRevenue, 2) }}</h5>
                                    <small class="text-muted">Total Revenue</small>
                                </div>
                                <div class="col-md-4 text-center">
                                    <h5 class="text-primary">{{ $totalQuantity }}</h5>
                                    <small class="text-muted">Units Sold</small>
                                </div>
                                <div class="col-md-4 text-center">
                                    <h5 class="text-info">${{ $totalQuantity > 0 ? number_format($totalRevenue / $totalQuantity, 2) : '0.00' }}</h5>
                                    <small class="text-muted">Avg. Price</small>
                                </div>
                            </div>
                        @endif
                    @elseif($category->type === 'expense')
                        @php
                            $totalExpenses = $category->expenses()
                                ->whereYear('expense_date', date('Y'))
                                ->where('status', 'approved')
                                ->sum('amount');
                            
                            $monthlyExpenses = $category->expenses()
                                ->whereYear('expense_date', date('Y'))
                                ->where('status', 'approved')
                                ->selectRaw('MONTH(expense_date) as month, SUM(amount) as total')
                                ->groupBy('month')
                                ->orderBy('month')
                                ->get();
                        @endphp
                        
                        @if($totalExpenses > 0)
                            <hr>
                            <h6>This Year's Expenses</h6>
                            <div class="row">
                                <div class="col-md-6 text-center">
                                    <h5 class="text-danger">${{ number_format($totalExpenses, 2) }}</h5>
                                    <small class="text-muted">Total Amount</small>
                                </div>
                                <div class="col-md-6 text-center">
                                    <h5 class="text-warning">${{ number_format($totalExpenses / 12, 2) }}</h5>
                                    <small class="text-muted">Monthly Average</small>
                                </div>
                            </div>
                            
                            @if($monthlyExpenses->count() > 0)
                                <div class="mt-3">
                                    <h6>Monthly Breakdown</h6>
                                    <div class="table-responsive">
                                        <table class="table table-sm">
                                            <thead>
                                                <tr>
                                                    <th>Month</th>
                                                    <th>Amount</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($monthlyExpenses as $expense)
                                                    <tr>
                                                        <td>{{ DateTime::createFromFormat('!m', $expense->month)->format('F') }}</td>
                                                        <td>${{ number_format($expense->total, 2) }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            @endif
                        @endif
                    @endif
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Quick Actions -->
            <div class="card shadow-sm mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-bolt"></i> Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        @can('create', App\Models\Category::class)
                            <a href="{{ route('categories.create') }}?parent_id={{ $category->id }}&type={{ $category->type }}" class="btn btn-success btn-sm">
                                <i class="fas fa-plus"></i> Add Subcategory
                            </a>
                        @endcan
                        
                        @can('update', $category)
                            <a href="{{ route('categories.edit', $category) }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-edit"></i> Edit Category
                            </a>
                        @endcan
                        
                        @if($category->type === 'product')
                            <a href="{{ route('products.index') }}?category_id={{ $category->id }}" class="btn btn-outline-info btn-sm">
                                <i class="fas fa-box"></i> View Products
                            </a>
                        @elseif($category->type === 'expense')
                            <a href="{{ route('expenses.index') }}?category_id={{ $category->id }}" class="btn btn-outline-warning btn-sm">
                                <i class="fas fa-receipt"></i> View Expenses
                            </a>
                        @endif
                        
                        @can('view', App\Models\Report::class)
                            <a href="{{ route('reports.index') }}?category_id={{ $category->id }}" class="btn btn-outline-secondary btn-sm">
                                <i class="fas fa-chart-bar"></i> View Reports
                            </a>
                        @endcan
                        
                        @can('delete', $category)
                            <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteModal">
                                <i class="fas fa-trash"></i> Delete Category
                            </button>
                        @endcan
                    </div>
                </div>
            </div>

            <!-- Category Tree -->
            @if($category->parent || $category->children->count() > 0)
                <div class="card shadow-sm mb-4">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-sitemap"></i> Category Hierarchy</h5>
                    </div>
                    <div class="card-body">
                        <div class="category-tree">
                            @if($category->parent)
                                <div class="mb-2">
                                    <small class="text-muted">Parent:</small><br>
                                    <a href="{{ route('categories.show', $category->parent) }}" class="text-decoration-none">
                                        {{ $category->parent->name }}
                                    </a>
                                </div>
                                <hr>
                            @endif
                            
                            <div class="mb-2">
                                <strong style="color: {{ $category->color }};">
                                    <i class="{{ $category->icon ?: 'fas fa-folder' }}"></i> {{ $category->name }}
                                </strong>
                            </div>
                            
                            @if($category->children->count() > 0)
                                <div class="ms-3">
                                    @foreach($category->children->sortBy('name') as $child)
                                        <div class="mb-1">
                                            <a href="{{ route('categories.show', $child) }}" class="text-decoration-none small">
                                                <span style="color: {{ $child->color }};">{{ $child->name }}</span>
                                            </a>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endif

            <!-- Settings Summary -->
            <div class="card shadow-sm mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-cogs"></i> Settings</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-6 mb-2">
                            <small class="text-muted">Active:</small><br>
                            <span class="badge bg-{{ $category->is_active ? 'success' : 'secondary' }}">
                                {{ $category->is_active ? 'Yes' : 'No' }}
                            </span>
                        </div>
                        <div class="col-6 mb-2">
                            <small class="text-muted">Taxable:</small><br>
                            <span class="badge bg-{{ $category->is_taxable ? 'success' : 'secondary' }}">
                                {{ $category->is_taxable ? 'Yes' : 'No' }}
                            </span>
                        </div>
                        <div class="col-6 mb-2">
                            <small class="text-muted">Default:</small><br>
                            <span class="badge bg-{{ $category->is_default ? 'info' : 'light text-dark' }}">
                                {{ $category->is_default ? 'Yes' : 'No' }}
                            </span>
                        </div>
                        <div class="col-6 mb-2">
                            <small class="text-muted">Menu:</small><br>
                            <span class="badge bg-{{ $category->show_in_menu ? 'primary' : 'light text-dark' }}">
                                {{ $category->show_in_menu ? 'Shown' : 'Hidden' }}
                            </span>
                        </div>
                    </div>
                    
                    @if($category->default_tax_rate > 0)
                        <hr>
                        <p class="mb-1"><strong>Default Tax Rate:</strong></p>
                        <span class="badge bg-warning">{{ number_format($category->default_tax_rate, 2) }}%</span>
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
                        <dd class="col-6">{{ $category->created_at->format('M d, Y') }}</dd>
                        
                        <dt class="col-6">Updated:</dt>
                        <dd class="col-6">{{ $category->updated_at->format('M d, Y H:i') }}</dd>
                        
                        <dt class="col-6">Depth:</dt>
                        <dd class="col-6">{{ $category->depth ?? 0 }}</dd>
                        
                        <dt class="col-6">ID:</dt>
                        <dd class="col-6">#{{ $category->id }}</dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
@can('delete', $category)
    <div class="modal fade" id="deleteModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Delete Category</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i>
                        <strong>Warning:</strong> This action cannot be undone.
                    </div>
                    
                    <p>Are you sure you want to delete the category "<strong>{{ $category->name }}</strong>"?</p>
                    
                    @if($category->products_count > 0 || $category->expenses_count > 0 || $category->children->count() > 0)
                        <div class="alert alert-danger">
                            <p class="mb-2"><strong>This category contains:</strong></p>
                            <ul class="mb-0">
                                @if($category->products_count > 0)
                                    <li>{{ $category->products_count }} product(s)</li>
                                @endif
                                @if($category->expenses_count > 0)
                                    <li>{{ $category->expenses_count }} expense(s)</li>
                                @endif
                                @if($category->children->count() > 0)
                                    <li>{{ $category->children->count() }} subcategory(ies)</li>
                                @endif
                            </ul>
                            <p class="mb-0 mt-2">You must move or delete these items before deleting this category.</p>
                        </div>
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <form action="{{ route('categories.destroy', $category) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger" 
                                {{ ($category->products_count > 0 || $category->expenses_count > 0 || $category->children->count() > 0) ? 'disabled' : '' }}>
                            Delete Category
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endcan

@endsection