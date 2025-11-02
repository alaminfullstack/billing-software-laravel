@extends('layouts.app')

@section('title', $expenseCategory->name)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1>{{ $expenseCategory->name }}</h1>
                <div>
                    <a href="{{ route('expense-categories.edit', $expenseCategory) }}" class="btn btn-primary me-2">
                        <i class="fas fa-edit"></i> Edit
                    </a>
                    <a href="{{ route('expense-categories.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back
                    </a>
                </div>
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="row">
                <div class="col-md-6">
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Category Details</h5>
                        </div>
                        <div class="card-body">
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Name:</strong></td>
                                    <td>{{ $expenseCategory->name }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Description:</strong></td>
                                    <td>{{ $expenseCategory->description ?: 'No description' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Expenses Count:</strong></td>
                                    <td>
                                        <span class="badge bg-info">{{ $expenseCategory->expenses_count }}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Created:</strong></td>
                                    <td>{{ $expenseCategory->created_at->format('F d, Y \a\t h:i A') }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Last Updated:</strong></td>
                                    <td>{{ $expenseCategory->updated_at->format('F d, Y \a\t h:i A') }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Quick Actions</h5>
                        </div>
                        <div class="card-body">
                            <div class="d-grid gap-2">
                                <a href="{{ route('expenses.create') }}" class="btn btn-success">
                                    <i class="fas fa-plus"></i> Add New Expense
                                </a>
                                <a href="{{ route('expenses.index') }}" class="btn btn-info">
                                    <i class="fas fa-list"></i> View All Expenses
                                </a>
                                <button type="button" class="btn btn-danger" onclick="deleteCategory()">
                                    <i class="fas fa-trash"></i> Delete Category
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Recent Expenses</h5>
                </div>
                <div class="card-body">
                    @if($expenseCategory->expenses->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Amount</th>
                                        <th>Description</th>
                                        <th>Creator</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($expenseCategory->expenses->take(10) as $expense)
                                        <tr>
                                            <td>{{ $expense->expense_date->format('M d, Y') }}</td>
                                            <td>${{ number_format($expense->amount, 2) }}</td>
                                            <td>{{ Str::limit($expense->description, 50) }}</td>
                                            <td>{{ $expense->creator->name ?? 'Unknown' }}</td>
                                            <td>
                                                <a href="{{ route('expenses.show', $expense) }}" 
                                                   class="btn btn-sm btn-outline-info">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @if($expenseCategory->expenses->count() > 10)
                            <div class="text-center mt-3">
                                <a href="{{ route('expenses.index') }}" class="btn btn-outline-primary">
                                    View All {{ $expenseCategory->expenses->count() }} Expenses
                                </a>
                            </div>
                        @endif
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-receipt fa-3x text-muted mb-3"></i>
                            <h5>No Expenses Found</h5>
                            <p class="text-muted">No expenses have been recorded for this category yet.</p>
                            <a href="{{ route('expenses.create') }}" class="btn btn-success">
                                <i class="fas fa-plus"></i> Add First Expense
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteCategoryModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete the expense category <strong>"{{ $expenseCategory->name }}"</strong>?</p>
                @if($expenseCategory->expenses->count() > 0)
                    <div class="alert alert-warning">
                        <strong>Warning:</strong> This category has {{ $expenseCategory->expenses->count() }} expense(s) associated with it.
                        You cannot delete a category that has expenses.
                    </div>
                @endif
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                @if($expenseCategory->expenses->count() == 0)
                    <form id="deleteCategoryForm" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Delete</button>
                    </form>
                @else
                    <button type="button" class="btn btn-danger" disabled>
                        Cannot Delete (Has Expenses)
                    </button>
                @endif
            </div>
        </div>
    </div>
</div>

<script>
function deleteCategory() {
    const modal = new bootstrap.Modal(document.getElementById('deleteCategoryModal'));
    modal.show();
}
</script>
@endsection
