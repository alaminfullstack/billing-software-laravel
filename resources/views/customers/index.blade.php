@extends('layouts.app')

@section('title', 'Customers')
@section('page-title', 'Customers')

@section('top-actions')
<a href="{{ route('customers.create') }}" class="btn btn-primary">
    <i class="bi bi-person-plus"></i> Add Customer
</a>
@endsection

@section('content')
<div class="card">
    <div class="card-body">
        <!-- Search and Filters -->
        <form method="GET" class="row mb-4">
            <div class="col-md-4">
                <input type="text" 
                       class="form-control" 
                       name="search" 
                       placeholder="Search customers..." 
                       value="{{ request('search') }}">
            </div>
            <div class="col-md-3">
                <select name="status" class="form-select">
                    <option value="">All Status</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    <option value="blocked" {{ request('status') == 'blocked' ? 'selected' : '' }}>Blocked</option>
                </select>
            </div>
            <div class="col-md-3">
                <select name="customer_type" class="form-select">
                    <option value="">All Types</option>
                    <option value="individual" {{ request('customer_type') == 'individual' ? 'selected' : '' }}>Individual</option>
                    <option value="business" {{ request('customer_type') == 'business' ? 'selected' : '' }}>Business</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-outline-primary w-100">
                    <i class="bi bi-search"></i> Search
                </button>
            </div>
        </form>
        
        <!-- Customers Table -->
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Type</th>
                        <th>Status</th>
                        <th>Created</th>
                        <th width="150">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($customers as $customer)
                    <tr>
                        <td>
                            <strong>{{ $customer->name }}</strong>
                            @if($customer->company_name)
                                <br><small class="text-muted">{{ $customer->company_name }}</small>
                            @endif
                        </td>
                        <td>{{ $customer->email }}</td>
                        <td>{{ $customer->phone ?? '-' }}</td>
                        <td>
                            <span class="badge bg-{{ $customer->customer_type == 'business' ? 'info' : 'secondary' }}">
                                {{ ucfirst($customer->customer_type) }}
                            </span>
                        </td>
                        <td>
                            <span class="status-badge status-{{ $customer->status }}">
                                {{ ucfirst($customer->status) }}
                            </span>
                        </td>
                        <td>{{ $customer->created_at->format('M d, Y') }}</td>
                        <td>
                            <div class="btn-group" role="group">
                                <a href="{{ route('customers.show', $customer) }}" 
                                   class="btn btn-sm btn-outline-primary" 
                                   title="View">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('customers.edit', $customer) }}" 
                                   class="btn btn-sm btn-outline-secondary" 
                                   title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <button type="button" 
                                        class="btn btn-sm btn-outline-danger" 
                                        title="Delete"
                                        onclick="deleteCustomer({{ $customer->id }})">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted">
                            <i class="bi bi-inbox"></i> No customers found
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        @if($customers->hasPages())
        <div class="d-flex justify-content-center">
            {{ $customers->links() }}
        </div>
        @endif
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete this customer? This action cannot be undone.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="deleteForm" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
function deleteCustomer(customerId) {
    const form = document.getElementById('deleteForm');
    form.action = `/customers/${customerId}`;
    
    const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
    modal.show();
}
</script>
@endsection
