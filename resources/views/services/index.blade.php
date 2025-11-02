@extends('layouts.app')

@section('title', 'Services')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1><i class="fas fa-concierge-bell"></i> Services</h1>
                <a href="{{ route('products.create', ['type' => 'service']) }}" class="btn btn-success">
                    <i class="fas fa-plus"></i> Add Service
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

            <!-- Service Statistics -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card bg-success text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="card-title">Total Services</h6>
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
                    <div class="card bg-primary text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="card-title">Active Services</h6>
                                    <h3 class="mb-0">{{ $activeServices ?? 0 }}</h3>
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
                                    <h6 class="card-title">Average Price</h6>
                                    <h3 class="mb-0">${{ number_format($averagePrice ?? 0, 2) }}</h3>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-dollar-sign fa-2x"></i>
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
                                    <h6 class="card-title">Most Popular</h6>
                                    <h3 class="mb-0">{{ $mostPopularService ?? 'N/A' }}</h3>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-star fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Search and Filters -->
            <div class="card mb-4">
                <div class="card-body">
                    <form method="GET" action="{{ route('services.index') }}" class="row g-3">
                        <div class="col-md-4">
                            <label for="search" class="form-label">Search Services</label>
                            <input type="text" name="search" id="search" class="form-control" 
                                   value="{{ request('search') }}" placeholder="Search by name or description...">
                        </div>
                        <div class="col-md-3">
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
                        <div class="col-md-2">
                            <label for="sort_by" class="form-label">Sort By</label>
                            <select name="sort_by" id="sort_by" class="form-select">
                                <option value="name" {{ request('sort_by', 'name') == 'name' ? 'selected' : '' }}>Name</option>
                                <option value="price" {{ request('sort_by') == 'price' ? 'selected' : '' }}>Price</option>
                                <option value="created_at" {{ request('sort_by') == 'created_at' ? 'selected' : '' }}>Date Created</option>
                                <option value="usage_count" {{ request('sort_by') == 'usage_count' ? 'selected' : '' }}>Most Used</option>
                            </select>
                        </div>
                        <div class="col-md-1">
                            <label class="form-label">&nbsp;</label>
                            <button type="submit" class="btn btn-outline-primary w-100">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                        <div class="col-12">
                            <button type="button" class="btn btn-outline-secondary" onclick="resetFilters()">
                                <i class="fas fa-times"></i> Clear
                            </button>
                            <button type="button" class="btn btn-outline-info" onclick="exportServices()">
                                <i class="fas fa-download"></i> Export
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Services Grid -->
            <div class="card">
                <div class="card-body">
                    @if(isset($services) && count($services) > 0)
                        <div class="row">
                            @foreach($services as $service)
                                <div class="col-md-6 col-lg-4 mb-4">
                                    <div class="card h-100 {{ $service->status == 'active' ? 'border-success' : 'border-secondary' }}">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-start mb-3">
                                                <div>
                                                    <h5 class="card-title mb-1">{{ $service->name }}</h5>
                                                    <p class="card-text text-muted small">{{ Str::limit($service->description, 80) }}</p>
                                                </div>
                                                <span class="badge bg-{{ $service->status == 'active' ? 'success' : 'secondary' }}">
                                                    {{ ucfirst($service->status) }}
                                                </span>
                                            </div>

                                            <div class="mb-3">
                                                <h4 class="text-success mb-0">${{ number_format($service->price, 2) }}</h4>
                                                @if($service->cost && $service->cost > 0)
                                                    <small class="text-muted">Cost: ${{ number_format($service->cost, 2) }}</small>
                                                    <br>
                                                    <small class="text-success">
                                                        Profit: ${{ number_format($service->price - $service->cost, 2) }}
                                                        ({{ number_format((($service->price - $service->cost) / $service->price) * 100, 1) }}%)
                                                    </small>
                                                @endif
                                            </div>

                                            <div class="mb-3">
                                                <small class="text-muted">Category:</small>
                                                <span class="badge bg-primary">{{ $service->category->name ?? 'No Category' }}</span>
                                            </div>

                                            @if($service->duration)
                                                <div class="mb-3">
                                                    <small class="text-muted">Duration:</small>
                                                    <span class="text-info">{{ $service->duration }} {{ $service->duration_unit ?? 'hours' }}</span>
                                                </div>
                                            @endif

                                            <div class="mb-3">
                                                <small class="text-muted">Usage:</small>
                                                <div class="d-flex justify-content-between">
                                                    <span class="text-info">{{ $service->usage_count ?? 0 }} times in invoices</span>
                                                    @if(($service->usage_count ?? 0) > 0)
                                                        <small class="text-success">
                                                            ${{ number_format(($service->usage_count ?? 0) * $service->price, 2) }} earned
                                                        </small>
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="d-flex justify-content-between align-items-center">
                                                <small class="text-muted">
                                                    <i class="fas fa-calendar"></i> {{ $service->created_at->format('M d, Y') }}
                                                </small>
                                                
                                                <div class="btn-group btn-group-sm">
                                                    <a href="{{ route('products.show', $service) }}" 
                                                       class="btn btn-outline-primary" title="View">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('products.edit', $service) }}" 
                                                       class="btn btn-outline-warning" title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <button type="button" class="btn btn-outline-info" 
                                                            onclick="duplicateService({{ $service->id }})" title="Duplicate">
                                                        <i class="fas fa-copy"></i>
                                                    </button>
                                                    <form method="POST" action="{{ route('products.destroy', $service) }}" 
                                                          style="display: inline;" 
                                                          onsubmit="return confirm('Are you sure you want to delete this service?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-outline-danger" title="Delete">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        @if($service->features && count($service->features) > 0)
                                            <div class="card-footer">
                                                <small class="text-muted">Features:</small>
                                                <div class="mt-2">
                                                    @foreach($service->features->take(3) as $feature)
                                                        <span class="badge bg-light text-dark me-1">{{ $feature }}</span>
                                                    @endforeach
                                                    @if(count($service->features) > 3)
                                                        <span class="badge bg-light text-dark">+{{ count($service->features) - 3 }} more</span>
                                                    @endif
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Pagination -->
                        @if(method_exists($services, 'links'))
                            <div class="d-flex justify-content-center">
                                {{ $services->links() }}
                            </div>
                        @endif
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-concierge-bell fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No services found</h5>
                            <p class="text-muted">Create services to offer to your customers.</p>
                            <a href="{{ route('products.create', ['type' => 'service']) }}" class="btn btn-success">
                                <i class="fas fa-plus"></i> Add First Service
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Service Performance Analytics -->
            @if(isset($serviceAnalytics) && count($serviceAnalytics) > 0)
                <div class="card mt-4">
                    <div class="card-header">
                        <h5><i class="fas fa-chart-bar"></i> Service Performance</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Service</th>
                                        <th>Times Sold</th>
                                        <th>Total Revenue</th>
                                        <th>Average Price</th>
                                        <th>Performance</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($serviceAnalytics as $service)
                                        <tr>
                                            <td>
                                                <strong>{{ $service['name'] }}</strong>
                                                <br><small class="text-muted">{{ $service['category'] }}</small>
                                            </td>
                                            <td>{{ $service['usage_count'] }}</td>
                                            <td class="text-success">${{ number_format($service['total_revenue'], 2) }}</td>
                                            <td>${{ number_format($service['average_price'], 2) }}</td>
                                            <td>
                                                <div class="progress" style="height: 20px;">
                                                    <div class="progress-bar bg-success" 
                                                         style="width: {{ $service['performance_percentage'] }}%">
                                                        {{ $service['performance_percentage'] }}%
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<style>
.card {
    transition: all 0.3s ease;
}

.card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.badge {
    font-size: 0.75em;
}

.progress {
    border-radius: 10px;
}

.btn-group-sm .btn {
    padding: 0.25rem 0.5rem;
    font-size: 0.875rem;
}
</style>

@section('scripts')
function resetFilters() {
    document.getElementById('search').value = '';
    document.getElementById('category').value = '';
    document.getElementById('status').value = '';
    document.getElementById('sort_by').value = 'name';
    window.location.href = '{{ route("services.index") }}';
}

function exportServices() {
    const url = new URL(window.location);
    url.searchParams.set('export', 'true');
    window.open(url.toString(), '_blank');
}

function duplicateService(id) {
    if (confirm('Are you sure you want to duplicate this service?')) {
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
@endsection
@endsection