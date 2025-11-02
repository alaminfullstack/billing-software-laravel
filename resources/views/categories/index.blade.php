@extends('layouts.app')

@section('title', 'Categories')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1><i class="fas fa-tags"></i> Categories</h1>
                <a href="{{ route('categories.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Add Category
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

            <!-- Category Statistics -->
            <div class="row mb-4">
                <div class="col-md-4">
                    <div class="card bg-primary text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="card-title">Total Categories</h6>
                                    <h3 class="mb-0">{{ $totalCategories ?? 0 }}</h3>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-tags fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card bg-success text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="card-title">Active Categories</h6>
                                    <h3 class="mb-0">{{ $activeCategories ?? 0 }}</h3>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-check-circle fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card bg-info text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="card-title">Most Used</h6>
                                    <h3 class="mb-0">{{ $mostUsedCategory ?? 'N/A' }}</h3>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-star fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Category Type Tabs -->
            <div class="card">
                <div class="card-header">
                    <ul class="nav nav-tabs card-header-tabs" id="categoryTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link {{ !request('type') || request('type') == 'product' ? 'active' : '' }}" 
                                    onclick="filterByType('product')">
                                <i class="fas fa-box"></i> Product Categories ({{ $productCategories ?? 0 }})
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link {{ request('type') == 'expense' ? 'active' : '' }}" 
                                    onclick="filterByType('expense')">
                                <i class="fas fa-receipt"></i> Expense Categories ({{ $expenseCategories ?? 0 }})
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link {{ request('type') == 'income' ? 'active' : '' }}" 
                                    onclick="filterByType('income')">
                                <i class="fas fa-money-bill"></i> Income Categories ({{ $incomeCategories ?? 0 }})
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link {{ request('type') == 'all' ? 'active' : '' }}" 
                                    onclick="filterByType('all')">
                                <i class="fas fa-list"></i> All Categories
                            </button>
                        </li>
                    </ul>
                </div>
                <div class="card-body">
                    @if(isset($categories) && count($categories) > 0)
                        <div class="row">
                            @foreach($categories as $category)
                                <div class="col-md-6 col-lg-4 mb-4">
                                    <div class="card h-100 {{ $category->is_active ? 'border-success' : 'border-secondary' }}">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-start mb-3">
                                                <div>
                                                    <h5 class="card-title mb-1">{{ $category->name }}</h5>
                                                    @if($category->description)
                                                        <p class="card-text text-muted small">{{ Str::limit($category->description, 100) }}</p>
                                                    @endif
                                                </div>
                                                <span class="badge bg-{{ $category->is_active ? 'success' : 'secondary' }}">
                                                    {{ $category->is_active ? 'Active' : 'Inactive' }}
                                                </span>
                                            </div>

                                            <div class="mb-3">
                                                <small class="text-muted">Type:</small>
                                                <span class="badge bg-primary">{{ ucfirst($category->type) }}</span>
                                            </div>

                                            @if($category->parent)
                                                <div class="mb-3">
                                                    <small class="text-muted">Parent:</small>
                                                    <span class="text-primary">{{ $category->parent->name }}</span>
                                                </div>
                                            @endif

                                            <div class="mb-3">
                                                <small class="text-muted">Usage:</small>
                                                <div class="progress" style="height: 6px;">
                                                    <div class="progress-bar bg-info" 
                                                         style="width: {{ min(100, ($category->usage_count / max(1, $maxUsage)) * 100) }}%"></div>
                                                </div>
                                                <small class="text-muted">{{ $category->usage_count ?? 0 }} items</small>
                                            </div>

                                            <div class="d-flex justify-content-between align-items-center">
                                                <small class="text-muted">
                                                    <i class="fas fa-calendar"></i> {{ $category->created_at->format('M d, Y') }}
                                                </small>
                                                
                                                <div class="btn-group btn-group-sm">
                                                    <a href="{{ route('categories.show', $category) }}" 
                                                       class="btn btn-outline-primary" title="View">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('categories.edit', $category) }}" 
                                                       class="btn btn-outline-warning" title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <form method="POST" action="{{ route('categories.destroy', $category) }}" 
                                                          style="display: inline;" 
                                                          onsubmit="return confirm('Are you sure you want to delete this category? Items using this category will not be deleted.')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-outline-danger" title="Delete">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        @if($category->color)
                                            <div class="card-footer" style="background-color: {{ $category->color }}20; border-top: 1px solid {{ $category->color }}40;">
                                                <small class="text-muted">
                                                    <i class="fas fa-palette" style="color: {{ $category->color }}"></i>
                                                    Color: {{ $category->color }}
                                                </small>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Pagination -->
                        @if(method_exists($categories, 'links'))
                            <div class="d-flex justify-content-center">
                                {{ $categories->links() }}
                            </div>
                        @endif
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-tags fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No categories found</h5>
                            <p class="text-muted">
                                @if(request('type') && request('type') != 'all')
                                    No {{ request('type') }} categories found. Create your first one to get started.
                                @else
                                    Create categories to organize your products, expenses, and income.
                                @endif
                            </p>
                            <a href="{{ route('categories.create', ['type' => request('type', 'product')]) }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Add First Category
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Category Tree View -->
            @if(isset($categoryTree) && count($categoryTree) > 0)
                <div class="card mt-4">
                    <div class="card-header">
                        <h5><i class="fas fa-sitemap"></i> Category Hierarchy</h5>
                    </div>
                    <div class="card-body">
                        <div class="tree-view">
                            @foreach($categoryTree as $category)
                                <div class="tree-item" style="margin-left: {{ ($category->depth ?? 0) * 20 }}px;">
                                    <div class="d-flex align-items-center p-2 border rounded mb-2">
                                        <i class="fas fa-folder text-primary me-2"></i>
                                        <strong>{{ $category->name }}</strong>
                                        <span class="badge bg-secondary ms-2">{{ ucfirst($category->type) }}</span>
                                        <span class="badge bg-info ms-2">{{ $category->children->count() ?? 0 }} children</span>
                                        <div class="ms-auto">
                                            <a href="{{ route('categories.edit', $category) }}" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        </div>
                                    </div>
                                    
                                    @if($category->children && count($category->children) > 0)
                                        @foreach($category->children as $child)
                                            <div class="tree-item" style="margin-left: {{ (($category->depth ?? 0) + 1) * 20 }}px;">
                                                <div class="d-flex align-items-center p-2 border rounded mb-2">
                                                    <i class="fas fa-folder-open text-success me-2"></i>
                                                    <strong>{{ $child->name }}</strong>
                                                    <span class="badge bg-secondary ms-2">{{ ucfirst($child->type) }}</span>
                                                    <div class="ms-auto">
                                                        <a href="{{ route('categories.edit', $child) }}" class="btn btn-sm btn-outline-primary">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<style>
.tree-view {
    max-height: 400px;
    overflow-y: auto;
}

.tree-item {
    transition: all 0.3s ease;
}

.tree-item:hover {
    background-color: #f8f9fc;
    border-radius: 8px;
}

.card {
    transition: all 0.3s ease;
}

.card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.nav-link {
    cursor: pointer;
}

.nav-link.active {
    background-color: #007bff;
    border-color: #007bff;
    color: white;
}

.progress {
    border-radius: 10px;
}

.badge {
    font-size: 0.75em;
}
</style>

@section('scripts')
function filterByType(type) {
    const url = new URL(window.location);
    if (type === 'all') {
        url.searchParams.delete('type');
    } else {
        url.searchParams.set('type', type);
    }
    window.location.href = url.toString();
}

function exportCategories() {
    const url = new URL(window.location);
    url.searchParams.set('export', 'true');
    window.open(url.toString(), '_blank');
}
@endsection
@endsection