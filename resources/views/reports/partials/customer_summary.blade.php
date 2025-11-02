<div class="card shadow-sm">
    <div class="card-header">
        <h5 class="mb-0"><i class="fas fa-users"></i> Customer Summary</h5>
    </div>
    <div class="card-body">
        @if(isset($customerSummaryData))
            <div class="row">
                <div class="col-md-3">
                    <div class="text-center p-3 border rounded">
                        <h6 class="text-primary">Total Customers</h6>
                        <h3 class="text-primary">{{ $customerSummaryData['total_customers'] ?? 0 }}</h3>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="text-center p-3 border rounded">
                        <h6 class="text-success">Active Customers</h6>
                        <h3 class="text-success">{{ $customerSummaryData['active_customers'] ?? 0 }}</h3>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="text-center p-3 border rounded">
                        <h6 class="text-info">Total Revenue</h6>
                        <h4 class="text-info">${{ number_format($customerSummaryData['total_revenue'] ?? 0, 2) }}</h4>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="text-center p-3 border rounded">
                        <h6 class="text-warning">Avg Order Value</h6>
                        <h4 class="text-warning">${{ number_format($customerSummaryData['avg_order_value'] ?? 0, 2) }}</h4>
                    </div>
                </div>
            </div>

            <div class="mt-4">
                <h6>Top Customers</h6>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Customer</th>
                                <th>Total Orders</th>
                                <th>Total Revenue</th>
                                <th>Last Order</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($customerSummaryData['top_customers'] ?? [] as $customer)
                                <tr>
                                    <td>{{ $customer['name'] ?? 'N/A' }}</td>
                                    <td>{{ $customer['total_orders'] ?? 0 }}</td>
                                    <td>${{ number_format($customer['total_revenue'] ?? 0, 2) }}</td>
                                    <td>{{ $customer['last_order'] ?? 'N/A' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="mt-4">
                <h6>Customer Aging Summary</h6>
                <div class="row">
                    <div class="col-md-3">
                        <div class="text-center p-2 border rounded">
                            <h6 class="text-success">Current</h6>
                            <h5 class="text-success">${{ number_format($customerSummaryData['aging']['current'] ?? 0, 2) }}</h5>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-center p-2 border rounded">
                            <h6 class="text-warning">1-30 Days</h6>
                            <h5 class="text-warning">${{ number_format($customerSummaryData['aging']['30_days'] ?? 0, 2) }}</h5>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-center p-2 border rounded">
                            <h6 class="text-danger">31-60 Days</h6>
                            <h5 class="text-danger">${{ number_format($customerSummaryData['aging']['60_days'] ?? 0, 2) }}</h5>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-center p-2 border rounded">
                            <h6 class="text-dark">60+ Days</h6>
                            <h5 class="text-dark">${{ number_format($customerSummaryData['aging']['90_days'] ?? 0, 2) }}</h5>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="text-center text-muted">
                <i class="fas fa-users fa-3x mb-3"></i>
                <p>Customer summary data will be displayed here</p>
            </div>
        @endif
    </div>
</div>
