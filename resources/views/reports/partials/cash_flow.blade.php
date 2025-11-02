<div class="card shadow-sm">
    <div class="card-header">
        <h5 class="mb-0"><i class="fas fa-exchange-alt"></i> Cash Flow Summary</h5>
    </div>
    <div class="card-body">
        @if(isset($cashFlowData))
            <div class="row">
                <div class="col-md-4">
                    <div class="text-center p-3 border rounded">
                        <h6 class="text-success">Operating Cash Flow</h6>
                        <h4 class="text-success">${{ number_format($cashFlowData['operating'] ?? 0, 2) }}</h4>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="text-center p-3 border rounded">
                        <h6 class="text-info">Investing Cash Flow</h6>
                        <h4 class="text-info">${{ number_format($cashFlowData['investing'] ?? 0, 2) }}</h4>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="text-center p-3 border rounded">
                        <h6 class="text-primary">Financing Cash Flow</h6>
                        <h4 class="text-primary">${{ number_format($cashFlowData['financing'] ?? 0, 2) }}</h4>
                    </div>
                </div>
            </div>
            <div class="mt-3">
                <div class="text-center p-3 bg-light rounded">
                    <h5>Net Cash Flow</h5>
                    <h3 class="{{ ($cashFlowData['net'] ?? 0) >= 0 ? 'text-success' : 'text-danger' }}">
                        ${{ number_format($cashFlowData['net'] ?? 0, 2) }}
                    </h3>
                </div>
            </div>
        @else
            <div class="text-center text-muted">
                <i class="fas fa-chart-line fa-3x mb-3"></i>
                <p>Cash flow data will be displayed here</p>
            </div>
        @endif
    </div>
</div>
