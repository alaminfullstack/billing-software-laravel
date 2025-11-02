@extends('layouts.app')

@section('title', 'Expenses')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1><i class="fas fa-receipt"></i> Expenses</h1>
                <a href="{{ route('expenses.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Add Expense
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

            <!-- Expense Statistics -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card bg-primary text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="card-title">This Month</h6>
                                    <h3 class="mb-0">${{ number_format($monthlyExpenses ?? 0, 2) }}</h3>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-calendar-alt fa-2x"></i>
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
                                    <h6 class="card-title">Total Expenses</h6>
                                    <h3 class="mb-0">{{ $totalExpenses ?? 0 }}</h3>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-receipt fa-2x"></i>
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
                                    <h6 class="card-title">Pending</h6>
                                    <h3 class="mb-0">${{ number_format($pendingExpenses ?? 0, 2) }}</h3>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-clock fa-2x"></i>
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
                                    <h6 class="card-title">Paid</h6>
                                    <h3 class="mb-0">${{ number_format($paidExpenses ?? 0, 2) }}</h3>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-check-circle fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filters -->
            <div class="card mb-4">
                <div class="card-body">
                    <form method="GET" action="{{ route('expenses.index') }}" class="row g-3">
                        <div class="col-md-3">
                            <label for="search" class="form-label">Search</label>
                            <input type="text" name="search" id="search" class="form-control" 
                                   value="{{ request('search') }}" placeholder="Search expenses...">
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
                                <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Paid</option>
                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="date_from" class="form-label">Date From</label>
                            <input type="date" name="date_from" id="date_from" class="form-control" value="{{ request('date_from') }}">
                        </div>
                        <div class="col-md-2">
                            <label for="date_to" class="form-label">Date To</label>
                            <input type="date" name="date_to" id="date_to" class="form-control" value="{{ request('date_to') }}">
                        </div>
                        <div class="col-md-1">
                            <label class="form-label">&nbsp;</label>
                            <button type="submit" class="btn btn-outline-primary w-100">
                                <i class="fas fa-filter"></i>
                            </button>
                        </div>
                        <div class="col-12">
                            <button type="button" class="btn btn-outline-secondary" onclick="resetFilters()">
                                <i class="fas fa-times"></i> Clear Filters
                            </button>
                            <button type="button" class="btn btn-outline-info" onclick="exportExpenses()">
                                <i class="fas fa-download"></i> Export
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Expenses Table -->
            <div class="card">
                <div class="card-body">
                    @if(isset($expenses) && count($expenses) > 0)
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>
                                            <input type="checkbox" id="select-all">
                                        </th>
                                        <th>Date</th>
                                        <th>Description</th>
                                        <th>Category</th>
                                        <th>Amount</th>
                                        <th>Vendor</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($expenses as $expense)
                                        <tr>
                                            <td>
                                                <input type="checkbox" class="expense-checkbox" value="{{ $expense->id }}">
                                            </td>
                                            <td>
                                                {{ $expense->expense_date->format('M d, Y') }}
                                                @if($expense->due_date && $expense->expense_date->isPast() && $expense->status == 'pending')
                                                    <br><small class="text-danger"><i class="fas fa-exclamation-triangle"></i> Overdue</small>
                                                @endif
                                            </td>
                                            <td>
                                                <strong>{{ $expense->description }}</strong>
                                                @if($expense->reference_number)
                                                    <br><small class="text-muted">Ref: {{ $expense->reference_number }}</small>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge bg-secondary">{{ $expense->category->name ?? 'No Category' }}</span>
                                            </td>
                                            <td>
                                                <strong>${{ number_format($expense->amount, 2) }}</strong>
                                                @if($expense->tax_amount > 0)
                                                    <br><small class="text-muted">+ ${{ number_format($expense->tax_amount, 2) }} tax</small>
                                                @endif
                                            </td>
                                            <td>
                                                {{ $expense->vendor ?? 'N/A' }}
                                                @if($expense->payment_method)
                                                    <br><small class="text-muted">{{ ucfirst($expense->payment_method) }}</small>
                                                @endif
                                            </td>
                                            <td>
                                                @php
                                                    $statusClass = [
                                                        'paid' => 'success',
                                                        'pending' => 'warning',
                                                        'cancelled' => 'danger'
                                                    ][$expense->status] ?? 'secondary';
                                                @endphp
                                                <span class="badge bg-{{ $statusClass }}">{{ ucfirst($expense->status) }}</span>
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('expenses.show', $expense) }}" 
                                                       class="btn btn-sm btn-outline-primary" title="View">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('expenses.edit', $expense) }}" 
                                                       class="btn btn-sm btn-outline-warning" title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    @if($expense->receipt_path)
                                                        <a href="{{ asset('storage/' . $expense->receipt_path) }}" 
                                                           target="_blank" class="btn btn-sm btn-outline-info" title="View Receipt">
                                                            <i class="fas fa-file-alt"></i>
                                                        </a>
                                                    @endif
                                                    <form method="POST" action="{{ route('expenses.destroy', $expense) }}" 
                                                          style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this expense?')">
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
                                    <button type="button" class="btn btn-sm btn-outline-success" onclick="bulkAction('mark-paid')">
                                        <i class="fas fa-check"></i> Mark as Paid
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-warning" onclick="bulkAction('mark-pending')">
                                        <i class="fas fa-clock"></i> Mark as Pending
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
                        @if(method_exists($expenses, 'links'))
                            <div class="d-flex justify-content-center">
                                {{ $expenses->links() }}
                            </div>
                        @endif
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-receipt fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No expenses found</h5>
                            <p class="text-muted">Track your business expenses by adding your first expense.</p>
                            <a href="{{ route('expenses.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Add First Expense
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Expense Categories Summary -->
            @if(isset($categorySummary) && count($categorySummary) > 0)
                <div class="card mt-4">
                    <div class="card-header">
                        <h5><i class="fas fa-chart-pie"></i> Expenses by Category</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @foreach($categorySummary as $category)
                                <div class="col-md-3">
                                    <div class="border rounded p-3 text-center">
                                        <h4 class="text-primary">${{ number_format($category['total'], 2) }}</h4>
                                        <p class="text-muted mb-0">{{ $category['name'] }}</p>
                                        <small class="text-muted">{{ $category['count'] }} expenses</small>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

@section('scripts')
<script>
$(document).ready(function() {
    // Select all checkbox
    $('#select-all').change(function() {
        $('.expense-checkbox').prop('checked', this.checked);
        toggleBulkActions();
    });

    // Individual checkboxes
    $('.expense-checkbox').change(function() {
        toggleBulkActions();
    });

    function toggleBulkActions() {
        const checkedCount = $('.expense-checkbox:checked').length;
        if (checkedCount > 0) {
            $('#bulk-actions').show();
        } else {
            $('#bulk-actions').hide();
        }
    }

    function bulkAction(action) {
        const selectedIds = $('.expense-checkbox:checked').map(function() {
            return this.value;
        }).get();

        if (selectedIds.length === 0) {
            alert('Please select at least one expense.');
            return;
        }

        let confirmMessage = '';
        switch(action) {
            case 'mark-paid':
                confirmMessage = 'Mark selected expenses as paid?';
                break;
            case 'mark-pending':
                confirmMessage = 'Mark selected expenses as pending?';
                break;
            case 'delete':
                confirmMessage = 'Are you sure you want to delete the selected expenses? This action cannot be undone.';
                break;
        }

        if (confirm(confirmMessage)) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '/expenses/bulk-' + action;
            
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
});

function resetFilters() {
    document.getElementById('search').value = '';
    document.getElementById('category').value = '';
    document.getElementById('status').value = '';
    document.getElementById('date_from').value = '';
    document.getElementById('date_to').value = '';
    window.location.href = '{{ route("expenses.index") }}';
}

function exportExpenses() {
    const url = new URL(window.location);
    url.searchParams.set('export', 'true');
    window.open(url.toString(), '_blank');
}
</script>
@endsection
@endsection