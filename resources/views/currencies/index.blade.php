<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Currency Management - Accounting System</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 20px; background-color: #f5f5f5; }
        .container { max-width: 1200px; margin: 0 auto; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; padding-bottom: 20px; border-bottom: 2px solid #e0e0e0; }
        .title { font-size: 28px; font-weight: bold; color: #333; }
        .btn { padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; text-decoration: none; display: inline-block; transition: background 0.3s; }
        .btn-primary { background: #007bff; color: white; }
        .btn-primary:hover { background: #0056b3; }
        .btn-success { background: #28a745; color: white; }
        .btn-success:hover { background: #1e7e34; }
        .btn-warning { background: #ffc107; color: black; }
        .btn-warning:hover { background: #e0a800; }
        .btn-danger { background: #dc3545; color: white; }
        .btn-danger:hover { background: #c82333; }
        .btn-outline-secondary { background: transparent; color: #6c757d; border: 1px solid #6c757d; }
        .btn-outline-secondary:hover { background: #6c757d; color: white; }
        .nav-links { display: flex; gap: 15px; }
        .alert { padding: 15px; margin: 20px 0; border-radius: 5px; }
        .alert-success { background-color: #d4edda; border-color: #c3e6cb; color: #155724; }
        .alert-error { background-color: #f8d7da; border-color: #f5c6cb; color: #721c24; }
        .table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        .table th, .table td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
        .table th { background-color: #f8f9fa; font-weight: bold; }
        .table tr:hover { background-color: #f5f5f5; }
        .badge { padding: 4px 8px; border-radius: 12px; font-size: 12px; font-weight: bold; }
        .badge-success { background: #28a745; color: white; }
        .badge-warning { background: #ffc107; color: black; }
        .badge-primary { background: #007bff; color: white; }
        .currency-symbol { font-size: 18px; font-weight: bold; }
        .base-currency { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 2px 8px; border-radius: 12px; font-size: 10px; margin-left: 8px; }
        .actions { display: flex; gap: 10px; }
        .stats { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; margin: 30px 0; }
        .stat-card { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 20px; border-radius: 8px; text-align: center; }
        .stat-value { font-size: 24px; font-weight: bold; margin-bottom: 5px; }
        .stat-label { font-size: 14px; opacity: 0.9; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1 class="title">Currency Management</h1>
            <nav class="nav-links">
                <a href="{{ route('financial-reports.index') }}" class="btn btn-outline-secondary">Financial Reports</a>
                <a href="{{ route('currencies.exchange-rates') }}" class="btn btn-outline-secondary">Exchange Rates</a>
                <a href="{{ route('currencies.create') }}" class="btn btn-primary">Add Currency</a>
            </nav>
        </div>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-error">{{ session('error') }}</div>
        @endif

        <div class="stats">
            <div class="stat-card">
                <div class="stat-value">{{ $currencies->count() }}</div>
                <div class="stat-label">Total Currencies</div>
            </div>
            <div class="stat-card">
                <div class="stat-value">{{ $currencies->where('is_active', true)->count() }}</div>
                <div class="stat-label">Active Currencies</div>
            </div>
            <div class="stat-card">
                <div class="stat-value">{{ $baseCurrency->code ?? 'N/A' }}</div>
                <div class="stat-label">Base Currency</div>
            </div>
        </div>

        <div style="display: flex; justify-content: space-between; align-items: center; margin: 30px 0;">
            <h2>Available Currencies</h2>
            <div class="actions">
                <a href="{{ route('currencies.update-exchange-rates') }}" class="btn btn-warning" onclick="return confirm('Update exchange rates from API?')">Update Exchange Rates</a>
                <a href="{{ route('currencies.seed-defaults') }}" class="btn btn-success" onclick="return confirm('Create default currencies and accounts?')">Seed Defaults</a>
            </div>
        </div>

        <table class="table">
            <thead>
                <tr>
                    <th>Code</th>
                    <th>Currency</th>
                    <th>Symbol</th>
                    <th>Decimals</th>
                    <th>Status</th>
                    <th>Base Currency</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($currencies as $currency)
                <tr>
                    <td>
                        <span class="badge badge-primary">{{ $currency->code }}</span>
                        @if($currency->is_base_currency)
                            <span class="base-currency">BASE</span>
                        @endif
                    </td>
                    <td>{{ $currency->name }}</td>
                    <td>
                        <span class="currency-symbol">{{ $currency->symbol }}</span>
                    </td>
                    <td>{{ $currency->decimal_places }}</td>
                    <td>
                        @if($currency->is_active)
                            <span class="badge badge-success">Active</span>
                        @else
                            <span class="badge badge-warning">Inactive</span>
                        @endif
                    </td>
                    <td>
                        @if($currency->is_base_currency)
                            <span class="badge badge-success">Yes</span>
                        @else
                            <span class="badge badge-warning">No</span>
                        @endif
                    </td>
                    <td>
                        <div class="actions">
                            <a href="{{ route('currencies.edit', $currency) }}" class="btn btn-primary" style="padding: 5px 10px; font-size: 12px;">Edit</a>
                            @if(!$currency->is_base_currency)
                            <form method="POST" action="{{ route('currencies.destroy', $currency) }}" style="display: inline;" onsubmit="return confirm('Delete this currency?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger" style="padding: 5px 10px; font-size: 12px;">Delete</button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div style="margin-top: 40px; padding: 20px; background: #f8f9fa; border-radius: 8px;">
            <h3>Quick Actions</h3>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 15px; margin-top: 15px;">
                <div>
                    <h4>Exchange Rate Management</h4>
                    <p>Update and manage currency exchange rates</p>
                    <a href="{{ route('currencies.exchange-rates') }}" class="btn btn-primary">Manage Rates</a>
                </div>
                <div>
                    <h4>Currency Converter</h4>
                    <p>Convert between different currencies</p>
                    <a href="#convert" class="btn btn-success" onclick="showConverter()">Convert Now</a>
                </div>
                <div>
                    <h4>Historical Rates</h4>
                    <p>View historical exchange rate data</p>
                    <a href="#historical" class="btn btn-warning">View History</a>
                </div>
                <div>
                    <h4>Settings</h4>
                    <p>Configure currency preferences</p>
                    <a href="{{ route('currencies.settings') }}" class="btn btn-outline-secondary">Settings</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Currency Converter Modal (Simple Implementation) -->
    <div id="converterModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000;">
        <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); background: white; padding: 30px; border-radius: 8px; max-width: 500px; width: 90%;">
            <h3>Currency Converter</h3>
            <form method="POST" action="{{ route('currencies.convert') }}">
                @csrf
                <div style="margin: 15px 0;">
                    <label>Amount:</label>
                    <input type="number" name="amount" step="0.01" min="0" required style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
                </div>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                    <div>
                        <label>From:</label>
                        <select name="from_currency" required style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
                            @foreach($currencies as $currency)
                                <option value="{{ $currency->code }}">{{ $currency->code }} - {{ $currency->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label>To:</label>
                        <select name="to_currency" required style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
                            @foreach($currencies as $currency)
                                <option value="{{ $currency->code }}">{{ $currency->code }} - {{ $currency->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div style="margin-top: 20px; display: flex; gap: 10px;">
                    <button type="submit" class="btn btn-primary">Convert</button>
                    <button type="button" onclick="hideConverter()" class="btn btn-outline-secondary">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    @if(isset($conversion_result))
    <script>
        alert('Conversion Result: {{ $conversion_result["amount"] }} {{ $conversion_result["from_currency"]->code }} = {{ number_format($conversion_result["converted_amount"], 2) }} {{ $conversion_result["to_currency"]->code }}');
    </script>
    @endif

    <script>
        function showConverter() {
            document.getElementById('converterModal').style.display = 'block';
        }
        
        function hideConverter() {
            document.getElementById('converterModal').style.display = 'none';
        }
    </script>
</body>
</html>