@extends('layouts.app')

@section('title', $service->name)

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div class="d-flex align-items-center">
                    @if($service->image)
                        <img src="{{ asset('storage/' . $service->image) }}" 
                             alt="{{ $service->name }}" 
                             class="rounded me-3" 
                             style="width: 80px; height: 80px; object-fit: cover;">
                    @else
                        <div class="bg-light rounded d-flex align-items-center justify-content-center me-3" 
                             style="width: 80px; height: 80px;">
                            <i class="fas fa-concierge-bell fa-2x text-muted"></i>
                        </div>
                    @endif
                    <div>
                        <h1 class="h3 mb-1">{{ $service->name }}</h1>
                        <p class="text-muted mb-0">
                            {{ $service->rate_type ? ucfirst($service->rate_type) : 'Service' }} | 
                            ${{ number_format($service->base_rate, 2) }} | 
                            <span class="badge bg-{{ $service->is_active ? 'success' : 'secondary' }}">
                                {{ $service->is_active ? 'Active' : 'Inactive' }}
                            </span>
                            @if($service->is_featured)
                                <span class="badge bg-info ms-1">Featured</span>
                            @endif
                        </p>
                    </div>
                </div>
                <div class="d-flex gap-2">
                    @can('update', $service)
                        <a href="{{ route('services.edit', $service) }}" class="btn btn-primary">
                            <i class="fas fa-edit"></i> Edit Service
                        </a>
                    @endcan
                    <a href="{{ route('services.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Services
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Main Content -->
        <div class="col-lg-8">
            <!-- Service Overview -->
            <div class="card shadow-sm mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-info-circle"></i> Service Overview</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <dl class="row">
                                <dt class="col-sm-4">Service Code:</dt>
                                <dd class="col-sm-8">{{ $service->service_code ?? 'N/A' }}</dd>
                                
                                <dt class="col-sm-4">Category:</dt>
                                <dd class="col-sm-8">
                                    @if($service->category)
                                        <a href="{{ route('categories.show', $service->category) }}" class="text-decoration-none">
                                            {{ $service->category->name }}
                                        </a>
                                    @else
                                        <span class="text-muted">No category</span>
                                    @endif
                                </dd>
                                
                                <dt class="col-sm-4">Rate Type:</dt>
                                <dd class="col-sm-8">
                                    <span class="badge bg-primary">{{ ucfirst($service->rate_type ?? 'Not specified') }}</span>
                                </dd>
                                
                                <dt class="col-sm-4">Base Rate:</dt>
                                <dd class="col-sm-8">
                                    <span class="fw-bold text-success">${{ number_format($service->base_rate, 2) }}</span>
                                </dd>
                                
                                <dt class="col-sm-4">Duration:</dt>
                                <dd class="col-sm-8">{{ $service->duration ?? 'Not specified' }}</dd>
                            </dl>
                        </div>
                        <div class="col-md-6">
                            <dl class="row">
                                <dt class="col-sm-4">Delivery Time:</dt>
                                <dd class="col-sm-8">{{ $service->delivery_time ?? 'Not specified' }}</dd>
                                
                                <dt class="col-sm-4">Tax Rate:</dt>
                                <dd class="col-sm-8">
                                    @if($service->tax_rate > 0)
                                        {{ number_format($service->tax_rate, 2) }}%
                                    @else
                                        <span class="text-muted">Not set</span>
                                    @endif
                                </dd>
                                
                                <dt class="col-sm-4">Revisions:</dt>
                                <dd class="col-sm-8">
                                    @if($service->has_revisions)
                                        {{ $service->max_revisions }} rounds included
                                    @else
                                        <span class="text-muted">Not included</span>
                                    @endif
                                </dd>
                                
                                <dt class="col-sm-4">Deposit:</dt>
                                <dd class="col-sm-8">
                                    @if($service->requires_deposit)
                                        {{ number_format($service->deposit_percentage, 0) }}% required
                                    @else
                                        <span class="text-muted">Not required</span>
                                    @endif
                                </dd>
                                
                                <dt class="col-sm-4">Appointment:</dt>
                                <dd class="col-sm-8">
                                    @if($service->requires_appointment)
                                        <span class="badge bg-warning">Required</span>
                                    @else
                                        <span class="badge bg-light text-dark">Not Required</span>
                                    @endif
                                </dd>
                            </dl>
                        </div>
                    </div>
                    
                    @if($service->description)
                        <hr>
                        <h6>Description</h6>
                        <p class="text-muted">{{ $service->description }}</p>
                    @endif
                </div>
            </div>

            <!-- Service Features -->
            @if($service->features)
                <div class="card shadow-sm mb-4">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-check-circle"></i> What's Included</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12">
                                @php
                                    $features = preg_split('/\r\n|\r|\n/', $service->features);
                                @endphp
                                <ul class="list-unstyled">
                                    @foreach($features as $feature)
                                        @if(trim($feature))
                                            <li class="mb-2">
                                                <i class="fas fa-check text-success me-2"></i>
                                                {{ trim($feature) }}
                                            </li>
                                        @endif
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Requirements -->
            @if($service->requirements)
                <div class="card shadow-sm mb-4">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-clipboard-list"></i> Requirements</h5>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i>
                            <strong>What we need from you:</strong>
                            <div class="mt-2">
                                {!! nl2br(e($service->requirements)) !!}
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Pricing Tiers -->
            @if($service->pricing_tiers && is_array($service->pricing_tiers) && count($service->pricing_tiers) > 0)
                <div class="card shadow-sm mb-4">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-tags"></i> Pricing Tiers</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @foreach($service->pricing_tiers as $tier)
                                <div class="col-md-6 mb-3">
                                    <div class="card border-primary">
                                        <div class="card-body">
                                            <h6 class="card-title text-primary">{{ $tier['name'] ?? 'Tier' }}</h6>
                                            <div class="d-flex justify-content-between align-items-center">
                                                <span class="text-muted">
                                                    @if(isset($tier['min']) && $tier['min'] > 1)
                                                        Min. {{ $tier['min'] }} units
                                                    @else
                                                        Standard rate
                                                    @endif
                                                </span>
                                                <span class="h5 text-success">
                                                    ${{ number_format($tier['rate'] ?? 0, 2) }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            <!-- Service Analytics -->
            <div class="card shadow-sm">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-chart-bar"></i> Service Performance</h5>
                </div>
                <div class="card-body">
                    @if($service->invoiceItems->count() > 0)
                        @php
                            $totalRevenue = $service->invoiceItems->sum('total');
                            $totalQuantity = $service->invoiceItems->sum('quantity');
                            $averagePrice = $totalQuantity > 0 ? $totalRevenue / $totalQuantity : 0;
                            
                            $monthlySales = $service->invoiceItems()
                                ->whereHas('invoice', function($query) {
                                    $query->whereYear('created_at', date('Y'));
                                })
                                ->selectRaw('MONTH(created_at) as month, SUM(quantity) as total_qty, SUM(total) as total_amount')
                                ->groupBy('month')
                                ->orderBy('month')
                                ->get();
                        @endphp
                        
                        <div class="row mb-4">
                            <div class="col-md-3 text-center">
                                <div class="border rounded p-3">
                                    <h3 class="mb-1 text-primary">{{ $totalQuantity }}</h3>
                                    <small class="text-muted">Times Sold</small>
                                </div>
                            </div>
                            <div class="col-md-3 text-center">
                                <div class="border rounded p-3">
                                    <h3 class="mb-1 text-success">${{ number_format($totalRevenue, 2) }}</h3>
                                    <small class="text-muted">Total Revenue</small>
                                </div>
                            </div>
                            <div class="col-md-3 text-center">
                                <div class="border rounded p-3">
                                    <h3 class="mb-1 text-info">${{ number_format($averagePrice, 2) }}</h3>
                                    <small class="text-muted">Average Price</small>
                                </div>
                            </div>
                            <div class="col-md-3 text-center">
                                <div class="border rounded p-3">
                                    <h3 class="mb-1 text-warning">{{ $service->invoiceItems->count() }}</h3>
                                    <small class="text-muted">Total Orders</small>
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
                                            <th>Average Price</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($monthlySales as $sale)
                                            <tr>
                                                <td>{{ DateTime::createFromFormat('!m', $sale->month)->format('F') }}</td>
                                                <td>{{ $sale->total_qty }}</td>
                                                <td>${{ number_format($sale->total_amount, 2) }}</td>
                                                <td>${{ number_format($sale->total_amount / $sale->total_qty, 2) }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif

                        <div class="mt-4">
                            <h6>Recent Transactions</h6>
                            @php
                                $recentTransactions = $service->invoiceItems()
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
                            <p class="text-muted">This service hasn't been sold yet.</p>
                        </div>
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
                        @can('create', App\Models\Invoice::class)
                            <a href="{{ route('invoices.create') }}?service_id={{ $service->id }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-file-invoice"></i> Create Invoice
                            </a>
                        @endcan
                        
                        @can('update', $service)
                            <a href="{{ route('services.edit', $service) }}" class="btn btn-outline-primary btn-sm">
                                <i class="fas fa-edit"></i> Edit Service
                            </a>
                        @endcan
                        
                        @can('view', App\Models\Report::class)
                            <a href="{{ route('reports.index') }}?service_id={{ $service->id }}" class="btn btn-outline-info btn-sm">
                                <i class="fas fa-chart-bar"></i> View Reports
                            </a>
                        @endcan
                        
                        @if($service->category)
                            <a href="{{ route('services.index') }}?category_id={{ $service->category_id }}" class="btn btn-outline-secondary btn-sm">
                                <i class="fas fa-tags"></i> View Category
                            </a>
                        @endif
                        
                        <button type="button" class="btn btn-outline-success btn-sm" onclick="duplicateService()">
                            <i class="fas fa-copy"></i> Duplicate Service
                        </button>
                    </div>
                </div>
            </div>

            <!-- Service Configuration -->
            <div class="card shadow-sm mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-cogs"></i> Configuration</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-6 mb-2">
                            <small class="text-muted">Active:</small><br>
                            <span class="badge bg-{{ $service->is_active ? 'success' : 'secondary' }}">
                                {{ $service->is_active ? 'Yes' : 'No' }}
                            </span>
                        </div>
                        <div class="col-6 mb-2">
                            <small class="text-muted">Featured:</small><br>
                            <span class="badge bg-{{ $service->is_featured ? 'info' : 'light text-dark' }}">
                                {{ $service->is_featured ? 'Yes' : 'No' }}
                            </span>
                        </div>
                        <div class="col-6 mb-2">
                            <small class="text-muted">Customizable:</small><br>
                            <span class="badge bg-{{ $service->allow_customization ? 'primary' : 'light text-dark' }}">
                                {{ $service->allow_customization ? 'Yes' : 'No' }}
                            </span>
                        </div>
                        <div class="col-6 mb-2">
                            <small class="text-muted">Recurring:</small><br>
                            <span class="badge bg-{{ $service->is_recurring ? 'warning' : 'light text-dark' }}">
                                {{ $service->is_recurring ? 'Yes' : 'No' }}
                            </span>
                        </div>
                    </div>
                    
                    @if($service->deposit_percentage > 0)
                        <hr>
                        <p class="mb-1"><strong>Deposit Required:</strong></p>
                        <span class="badge bg-warning">{{ number_format($service->deposit_percentage, 0) }}%</span>
                    @endif
                    
                    @if($service->has_revisions)
                        <hr>
                        <p class="mb-1"><strong>Revisions:</strong></p>
                        <span class="badge bg-info">{{ $service->max_revisions }} rounds included</span>
                    @endif
                </div>
            </div>

            <!-- Customer Feedback -->
            @if($service->invoiceItems->count() > 0)
                <div class="card shadow-sm mb-4">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-comments"></i> Customer Feedback</h5>
                    </div>
                    <div class="card-body">
                        @php
                            $averageRating = $service->averageRating();
                        @endphp
                        
                        @if($averageRating)
                            <div class="text-center mb-3">
                                <h4 class="text-warning">{{ $averageRating }}</h4>
                                <div class="text-warning">
                                    @for($i = 1; $i <= 5; $i++)
                                        @if($i <= round($averageRating))
                                            <i class="fas fa-star"></i>
                                        @else
                                            <i class="far fa-star"></i>
                                        @endif
                                    @endfor
                                </div>
                                <small class="text-muted">Average Rating</small>
                            </div>
                        @else
                            <div class="text-center py-3">
                                <i class="fas fa-star fa-2x text-muted mb-2"></i>
                                <p class="text-muted mb-0">No ratings yet</p>
                            </div>
                        @endif
                        
                        @php
                            $recentReviews = $service->getRecentReviews(3);
                        @endphp
                        
                        @if($recentReviews->count() > 0)
                            <div class="mt-3">
                                <h6 class="mb-2">Recent Reviews</h6>
                                @foreach($recentReviews as $review)
                                    <div class="mb-3 p-2 border rounded">
                                        <div class="d-flex justify-content-between align-items-start mb-1">
                                            <strong>{{ $review['customer'] }}</strong>
                                            <small class="text-muted">{{ $review['date'] }}</small>
                                        </div>
                                        <div class="text-warning mb-1">
                                            @for($i = 1; $i <= 5; $i++)
                                                @if($i <= $review['rating'])
                                                    <i class="fas fa-star"></i>
                                                @else
                                                    <i class="far fa-star"></i>
                                                @endif
                                            @endfor
                                        </div>
                                        <p class="mb-0 small">{{ $review['comment'] }}</p>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            <!-- System Information -->
            <div class="card shadow-sm">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-info"></i> System Information</h5>
                </div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-6">Created:</dt>
                        <dd class="col-6">{{ $service->created_at->format('M d, Y') }}</dd>
                        
                        <dt class="col-6">Updated:</dt>
                        <dd class="col-6">{{ $service->updated_at->format('M d, Y H:i') }}</dd>
                        
                        <dt class="col-6">ID:</dt>
                        <dd class="col-6">#{{ $service->id }}</dd>
                        
                        @if($service->created_by)
                            <dt class="col-6">Created By:</dt>
                            <dd class="col-6">{{ $service->creator->name ?? 'Unknown' }}</dd>
                        @endif
                    </dl>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function duplicateService() {
    if (confirm('Are you sure you want to duplicate this service?')) {
        window.location.href = '{{ route("services.create") }}?duplicate={{ $service->id }}';
    }
}
</script>
@endsection