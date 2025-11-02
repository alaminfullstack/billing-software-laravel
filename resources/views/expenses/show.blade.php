@extends('layouts.app')

@section('title', $expense->title)

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-1">{{ $expense->title }}</h1>
                    <p class="text-muted mb-0">
                        ${{ number_format($expense->amount, 2) }} | 
                        {{ $expense->expense_date->format('M d, Y') }} | 
                        <span class="badge bg-{{ $expense->status === 'approved' ? 'success' : ($expense->status === 'rejected' ? 'danger' : 'warning') }}">
                            {{ ucfirst($expense->status) }}
                        </span>
                    </p>
                </div>
                <div class="d-flex gap-2">
                    @can('update', $expense)
                        <a href="{{ route('expenses.edit', $expense) }}" class="btn btn-primary">
                            <i class="fas fa-edit"></i> Edit Expense
                        </a>
                    @endcan
                    <a href="{{ route('expenses.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Expenses
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Main Content -->
        <div class="col-lg-8">
            <!-- Expense Overview -->
            <div class="card shadow-sm mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-info-circle"></i> Expense Overview</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <dl class="row">
                                <dt class="col-sm-4">Title:</dt>
                                <dd class="col-sm-8">{{ $expense->title }}</dd>
                                
                                <dt class="col-sm-4">Amount:</dt>
                                <dd class="col-sm-8">
                                    <span class="fw-bold text-success">${{ number_format($expense->amount, 2) }}</span>
                                </dd>
                                
                                <dt class="col-sm-4">Date:</dt>
                                <dd class="col-sm-8">{{ $expense->expense_date->format('M d, Y') }}</dd>
                                
                                <dt class="col-sm-4">Category:</dt>
                                <dd class="col-sm-8">
                                    @if($expense->category)
                                        {{ $expense->category->name }}
                                    @else
                                        <span class="text-muted">No category</span>
                                    @endif
                                </dd>
                                
                                <dt class="col-sm-4">Vendor:</dt>
                                <dd class="col-sm-8">{{ $expense->vendor ?? 'N/A' }}</dd>
                            </dl>
                        </div>
                        <div class="col-md-6">
                            <dl class="row">
                                <dt class="col-sm-4">Payment:</dt>
                                <dd class="col-sm-8">
                                    @if($expense->payment_method)
                                        {{ ucwords(str_replace('_', ' ', $expense->payment_method)) }}
                                    @else
                                        <span class="text-muted">Not specified</span>
                                    @endif
                                </dd>
                                
                                <dt class="col-sm-4">Reference:</dt>
                                <dd class="col-sm-8">{{ $expense->reference_number ?? 'N/A' }}</dd>
                                
                                <dt class="col-sm-4">Location:</dt>
                                <dd class="col-sm-8">{{ $expense->location ?? 'N/A' }}</dd>
                                
                                <dt class="col-sm-4">Project:</dt>
                                <dd class="col-sm-8">{{ $expense->project ?? 'N/A' }}</dd>
                                
                                <dt class="col-sm-4">Status:</dt>
                                <dd class="col-sm-8">
                                    <span class="badge bg-{{ $expense->status === 'approved' ? 'success' : ($expense->status === 'rejected' ? 'danger' : 'warning') }}">
                                        {{ ucfirst($expense->status) }}
                                    </span>
                                </dd>
                            </dl>
                        </div>
                    </div>
                    
                    @if($expense->description)
                        <hr>
                        <h6>Description</h6>
                        <p class="text-muted">{{ $expense->description }}</p>
                    @endif
                </div>
            </div>

            <!-- Tax Information -->
            <div class="card shadow-sm mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-calculator"></i> Tax Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 text-center">
                            <div class="border rounded p-3">
                                <h3 class="mb-1 text-primary">${{ number_format($expense->amount, 2) }}</h3>
                                <small class="text-muted">Subtotal</small>
                            </div>
                        </div>
                        <div class="col-md-4 text-center">
                            <div class="border rounded p-3">
                                <h3 class="mb-1 text-info">${{ number_format($expense->tax_amount, 2) }}</h3>
                                <small class="text-muted">Tax Amount</small>
                            </div>
                        </div>
                        <div class="col-md-4 text-center">
                            <div class="border rounded p-3">
                                <h3 class="mb-1 text-success">${{ number_format($expense->amount + $expense->tax_amount, 2) }}</h3>
                                <small class="text-muted">Total</small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <p><strong>Tax Rate:</strong> 
                                @if($expense->amount > 0)
                                    {{ number_format(($expense->tax_amount / $expense->amount) * 100, 2) }}%
                                @else
                                    0%
                                @endif
                            </p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Tax Deductible:</strong> 
                                @if($expense->is_tax_deductible)
                                    <span class="badge bg-success">Yes</span>
                                @else
                                    <span class="badge bg-secondary">No</span>
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Receipt -->
            @if($expense->receipt_path)
                <div class="card shadow-sm mb-4">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-paperclip"></i> Receipt</h5>
                    </div>
                    <div class="card-body">
                        <div class="text-center">
                            @php
                                $fileExtension = pathinfo($expense->receipt_path, PATHINFO_EXTENSION);
                            @endphp
                            
                            @if(in_array(strtolower($fileExtension), ['jpg', 'jpeg', 'png', 'gif']))
                                <img src="{{ asset('storage/' . $expense->receipt_path) }}" 
                                     alt="Expense receipt" 
                                     class="img-fluid rounded" 
                                     style="max-height: 500px; cursor: pointer;" 
                                     onclick="window.open('{{ asset('storage/' . $expense->receipt_path) }}', '_blank')">
                                <p class="mt-2">
                                    <small class="text-muted">Click image to view full size</small>
                                </p>
                            @elseif(strtolower($fileExtension) === 'pdf')
                                <div class="d-flex flex-column align-items-center">
                                    <i class="fas fa-file-pdf fa-5x text-danger mb-3"></i>
                                    <a href="{{ asset('storage/' . $expense->receipt_path) }}" 
                                       target="_blank" 
                                       class="btn btn-outline-danger">
                                        <i class="fas fa-external-link-alt"></i> View PDF Receipt
                                    </a>
                                </div>
                            @else
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle"></i> Receipt file type not supported for preview
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endif

            <!-- Approval History -->
            @if($expense->approval_history && count($expense->approval_history) > 0)
                <div class="card shadow-sm">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-history"></i> Approval History</h5>
                    </div>
                    <div class="card-body">
                        <div class="timeline">
                            @foreach($expense->approval_history as $history)
                                <div class="timeline-item mb-4">
                                    <div class="d-flex">
                                        <div class="flex-shrink-0">
                                            @if($history['action'] === 'approved')
                                                <div class="bg-success rounded-circle p-2">
                                                    <i class="fas fa-check text-white"></i>
                                                </div>
                                            @elseif($history['action'] === 'rejected')
                                                <div class="bg-danger rounded-circle p-2">
                                                    <i class="fas fa-times text-white"></i>
                                                </div>
                                            @else
                                                <div class="bg-warning rounded-circle p-2">
                                                    <i class="fas fa-clock text-white"></i>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <div class="d-flex justify-content-between align-items-start">
                                                <div>
                                                    <h6 class="mb-1">
                                                        @if($history['action'] === 'approved')
                                                            Expense Approved
                                                        @elseif($history['action'] === 'rejected')
                                                            Expense Rejected
                                                        @else
                                                            Expense Submitted
                                                        @endif
                                                    </h6>
                                                    <p class="text-muted mb-1">{{ $history['user'] ?? 'System' }}</p>
                                                    @if($history['notes'])
                                                        <p class="mb-0">{{ $history['notes'] }}</p>
                                                    @endif
                                                </div>
                                                <small class="text-muted">{{ \Carbon\Carbon::parse($history['date'])->format('M d, Y H:i') }}</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
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
                        @can('update', $expense)
                            <a href="{{ route('expenses.edit', $expense) }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-edit"></i> Edit Expense
                            </a>
                        @endcan
                        
                        @if($expense->receipt_path)
                            <a href="{{ asset('storage/' . $expense->receipt_path) }}" 
                               target="_blank" 
                               class="btn btn-outline-info btn-sm">
                                <i class="fas fa-download"></i> Download Receipt
                            </a>
                        @endif
                        
                        <a href="{{ route('expenses.create') }}?duplicate={{ $expense->id }}" class="btn btn-outline-success btn-sm">
                            <i class="fas fa-copy"></i> Duplicate Expense
                        </a>
                        
                        @can('view', App\Models\Report::class)
                            <a href="{{ route('reports.index') }}?expense_category_id={{ $expense->category_id }}" class="btn btn-outline-warning btn-sm">
                                <i class="fas fa-chart-bar"></i> View Category Reports
                            </a>
                        @endcan
                        
                        @if($expense->project)
                            <a href="{{ route('expenses.index') }}?project={{ urlencode($expense->project) }}" class="btn btn-outline-secondary btn-sm">
                                <i class="fas fa-project-diagram"></i> View Project Expenses
                            </a>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Approval Actions -->
            @if(auth()->user()->can('approve-expenses') && $expense->status === 'pending')
                <div class="card shadow-sm mb-4">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-user-check"></i> Approval Actions</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('expenses.update-status', $expense) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            
                            <div class="mb-3">
                                <label for="approval_notes" class="form-label">Notes (Optional)</label>
                                <textarea class="form-control" id="approval_notes" name="notes" rows="3" 
                                          placeholder="Add approval notes..."></textarea>
                            </div>
                            
                            <div class="d-grid gap-2">
                                <button type="submit" name="action" value="approved" class="btn btn-success">
                                    <i class="fas fa-check"></i> Approve Expense
                                </button>
                                <button type="submit" name="action" value="rejected" class="btn btn-danger">
                                    <i class="fas fa-times"></i> Reject Expense
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            @endif

            <!-- Expense Analytics -->
            <div class="card shadow-sm mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-chart-pie"></i> Expense Analysis</h5>
                </div>
                <div class="card-body">
                    @php
                        $monthlyExpenses = \App\Models\Expense::whereYear('expense_date', date('Y'))
                            ->where('category_id', $expense->category_id)
                            ->where('status', 'approved')
                            ->selectRaw('MONTH(expense_date) as month, SUM(amount + tax_amount) as total')
                            ->groupBy('month')
                            ->orderBy('month')
                            ->get();
                        
                        $categoryTotal = $monthlyExpenses->sum('total');
                        $currentMonthExpense = $monthlyExpenses->where('month', date('n'))->first();
                    @endphp
                    
                    <div class="row text-center mb-3">
                        <div class="col-6">
                            <div class="border-end">
                                <h4 class="text-primary">${{ number_format($currentMonthExpense ? $currentMonthExpense->total : 0, 2) }}</h4>
                                <small class="text-muted">This Month</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <h4 class="text-info">${{ number_format($categoryTotal, 2) }}</h4>
                            <small class="text-muted">Category Total</small>
                        </div>
                    </div>
                    
                    @if($expense->tax_amount > 0)
                        <div class="mb-2">
                            <strong>Tax Impact:</strong>
                            <div class="progress">
                                <div class="progress-bar" 
                                     style="width: {{ ($expense->tax_amount / ($expense->amount + $expense->tax_amount)) * 100 }}%">
                                    {{ number_format(($expense->tax_amount / ($expense->amount + $expense->tax_amount)) * 100, 1) }}%
                                </div>
                            </div>
                        </div>
                    @endif
                    
                    @if($expense->is_tax_deductible)
                        <div class="alert alert-success mt-3">
                            <i class="fas fa-check-circle"></i>
                            <strong>Tax Deductible</strong><br>
                            <small>This expense can be claimed for tax purposes</small>
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
                        <dd class="col-6">{{ $expense->created_at->format('M d, Y H:i') }}</dd>
                        
                        <dt class="col-6">Updated:</dt>
                        <dd class="col-6">{{ $expense->updated_at->format('M d, Y H:i') }}</dd>
                        
                        @if($expense->approved_at)
                            <dt class="col-6">Approved:</dt>
                            <dd class="col-6">{{ $expense->approved_at->format('M d, Y H:i') }}</dd>
                        @endif
                        
                        <dt class="col-6">ID:</dt>
                        <dd class="col-6">#{{ $expense->id }}</dd>
                        
                        @if($expense->created_by)
                            <dt class="col-6">Created By:</dt>
                            <dd class="col-6">{{ $expense->creator->name ?? 'Unknown' }}</dd>
                        @endif
                    </dl>
                </div>
            </div>
        </div>
    </div>
</div>

@if(auth()->user()->can('approve-expenses') && $expense->status === 'pending')
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Confirmation for approval actions
        const forms = document.querySelectorAll('form[action*="update-status"]');
        forms.forEach(form => {
            form.addEventListener('submit', function(e) {
                const action = e.submitter.value;
                const message = action === 'approved' ? 
                    'Are you sure you want to approve this expense?' : 
                    'Are you sure you want to reject this expense?';
                
                if (!confirm(message)) {
                    e.preventDefault();
                }
            });
        });
    });
    </script>
@endif

@endsection