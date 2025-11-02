@extends('layouts.app')

@section('title', 'Tax Rates')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1><i class="fas fa-percentage"></i> Tax Rates</h1>
                <a href="{{ route('taxes.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Add Tax Rate
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
                                    <h6 class="card-title">Total Taxes</h6>
                                    <h3 class="mb-0">{{ $stats['total'] }}</h3>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-percentage fa-2x"></i>
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
                                    <h6 class="card-title">Percentage</h6>
                                    <h3 class="mb-0">{{ $stats['percentage'] }}</h3>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-chart-line fa-2x"></i>
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
                                    <h6 class="card-title">Fixed</h6>
                                    <h3 class="mb-0">{{ $stats['fixed'] }}</h3>
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
                        <div class="col-md-4">
                            <label for="type" class="form-label">Type</label>
                            <select class="form-select" id="type" name="type">
                                <option value="">All Types</option>
                                <option value="percentage" {{ request('type') == 'percentage' ? 'selected' : '' }}>Percentage</option>
                                <option value="fixed" {{ request('type') == 'fixed' ? 'selected' : '' }}>Fixed Amount</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="search" class="form-label">Search</label>
                            <input type="text" class="form-control" id="search" name="search" 
                                   placeholder="Tax name or code..." 
                                   value="{{ request('search') }}">
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary me-2">
                                <i class="fas fa-search"></i> Filter
                            </button>
                            <a href="{{ route('taxes.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times"></i> Clear
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Taxes Table -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Tax Rates List ({{ $taxes->total() }})</h5>
                </div>
                <div class="card-body">
                    @if($taxes->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Tax Name</th>
                                        <th>Code</th>
                                        <th>Rate</th>
                                        <th>Type</th>
                                        <th>Status</th>
                                        <th>Description</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($taxes as $tax)
                                        <tr>
                                            <td>
                                                <strong>{{ $tax->name }}</strong>
                                            </td>
                                            <td>
                                                <code>{{ $tax->code }}</code>
                                            </td>
                                            <td>
                                                <strong>{{ $tax->formatted_rate }}</strong>
                                            </td>
                                            <td>
                                                <span class="badge bg-{{ $tax->type === 'percentage' ? 'info' : 'warning' }}">
                                                    {{ ucfirst($tax->type) }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge bg-{{ $tax->is_active ? 'success' : 'secondary' }}">
                                                    {{ $tax->is_active ? 'Active' : 'Inactive' }}
                                                </span>
                                            </td>
                                            <td>
                                                <small class="text-muted">
                                                    {{ Str::limit($tax->description ?? 'No description', 50) }}
                                                </small>
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('taxes.show', $tax) }}" 
                                                       class="btn btn-sm btn-outline-primary">
                                                        <i class="fas fa-eye"></i> View
                                                    </a>
                                                    <a href="{{ route('taxes.edit', $tax) }}" 
                                                       class="btn btn-sm btn-outline-secondary">
                                                        <i class="fas fa-edit"></i> Edit
                                                    </a>
                                                    <form method="POST" 
                                                          action="{{ route('taxes.toggle', $tax) }}" 
                                                          class="d-inline">
                                                        @csrf
                                                        <button type="submit" 
                                                                class="btn btn-sm btn-outline-{{ $tax->is_active ? 'warning' : 'success' }}"
                                                                onclick="return confirm('{{ $tax->is_active ? 'Deactivate' : 'Activate' }} this tax rate?')">
                                                            <i class="fas fa-{{ $tax->is_active ? 'pause' : 'play' }}"></i>
                                                            {{ $tax->is_active ? 'Deactivate' : 'Activate' }}
                                                        </button>
                                                    </form>
                                                    <form method="POST" 
                                                          action="{{ route('taxes.destroy', $tax) }}" 
                                                          class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" 
                                                                class="btn btn-sm btn-outline-danger"
                                                                onclick="return confirm('Delete this tax rate? This action cannot be undone.')">
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
                        
                        {{ $taxes->links() }}
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-percentage fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No tax rates found</h5>
                            <p class="text-muted">Create your first tax rate to get started.</p>
                            <a href="{{ route('taxes.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Add Tax Rate
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
