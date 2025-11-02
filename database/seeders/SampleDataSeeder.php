<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Customer;
use App\Models\Supplier;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Payment;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\User;
use App\Models\UserProfile;
use App\Models\CompanySetting;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;

class SampleDataSeeder extends Seeder
{
    public function run()
    {
        echo "Creating roles, permissions, and sample data...\n";
        
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
        
        // Create permissions
        $this->createPermissions();
        
        // Create roles and assign permissions
        $this->createRoles();
        
        // Create users with roles
        $this->createUsers();
        
        // Create expense categories
        $this->createExpenseCategories();
        
        // Create sample customers, suppliers, and transactions
        $this->createSampleData();
        
        echo "\n✅ Database seeded successfully!\n";
        echo "📊 Created users, roles, permissions, and sample financial data.\n";
        echo "🔐 Users created:\n";
        echo "   - admin@company.com (Admin role)\n";
        echo "   - accountant@company.com (Accountant role)\n";
        echo "   - user@company.com (User role)\n";
        echo "📈 Sample data created:\n";
        echo "   - " . Invoice::count() . " invoices (" . Invoice::where('status', 'paid')->count() . " paid, " . Invoice::where('status', '!=', 'paid')->count() . " unpaid)\n";
        echo "   - " . Payment::count() . " payments\n";
        echo "   - " . Expense::count() . " expenses\n";
        echo "   - " . Customer::count() . " customers\n";
        echo "   - " . ExpenseCategory::count() . " expense categories\n\n";
    }
    
    private function createPermissions()
    {
        echo "Creating permissions...\n";
        
        // Dashboard permissions
        Permission::create(['name' => 'view_dashboard']);
        
        // Invoice permissions
        Permission::create(['name' => 'view_invoices']);
        Permission::create(['name' => 'create_invoices']);
        Permission::create(['name' => 'edit_invoices']);
        Permission::create(['name' => 'delete_invoices']);
        Permission::create(['name' => 'send_invoices']);
        
        // Payment permissions
        Permission::create(['name' => 'view_payments']);
        Permission::create(['name' => 'create_payments']);
        Permission::create(['name' => 'edit_payments']);
        Permission::create(['name' => 'delete_payments']);
        
        // Expense permissions
        Permission::create(['name' => 'view_expenses']);
        Permission::create(['name' => 'create_expenses']);
        Permission::create(['name' => 'edit_expenses']);
        Permission::create(['name' => 'delete_expenses']);
        Permission::create(['name' => 'approve_expenses']);
        
        // Customer permissions
        Permission::create(['name' => 'view_customers']);
        Permission::create(['name' => 'create_customers']);
        Permission::create(['name' => 'edit_customers']);
        Permission::create(['name' => 'delete_customers']);
        
        // Supplier permissions
        Permission::create(['name' => 'view_suppliers']);
        Permission::create(['name' => 'create_suppliers']);
        Permission::create(['name' => 'edit_suppliers']);
        Permission::create(['name' => 'delete_suppliers']);
        
        // Financial Reports permissions
        Permission::create(['name' => 'view_financial_reports']);
        Permission::create(['name' => 'export_reports']);
        Permission::create(['name' => 'view_balance_sheet']);
        Permission::create(['name' => 'view_cash_flow']);
        Permission::create(['name' => 'view_trial_balance']);
        Permission::create(['name' => 'view_aging_reports']);
        Permission::create(['name' => 'view_tax_reports']);
        
        // Settings permissions
        Permission::create(['name' => 'view_settings']);
        Permission::create(['name' => 'edit_settings']);
        Permission::create(['name' => 'manage_users']);
        Permission::create(['name' => 'manage_roles']);
        
        // Multi-currency permissions
        Permission::create(['name' => 'view_currencies']);
        Permission::create(['name' => 'manage_currencies']);
        Permission::create(['name' => 'update_exchange_rates']);
    }
    
    private function createRoles()
    {
        echo "Creating roles...\n";
        
        // Admin role - All permissions
        $adminRole = Role::create(['name' => 'admin']);
        $adminRole->givePermissionTo(Permission::all());
        
        // Accountant role - Financial management
        $accountantRole = Role::create(['name' => 'accountant']);
        $accountantRole->givePermissionTo([
            // Dashboard
            'view_dashboard',
            
            // Financial operations
            'view_invoices', 'create_invoices', 'edit_invoices', 'send_invoices',
            'view_payments', 'create_payments', 'edit_payments',
            'view_expenses', 'create_expenses', 'edit_expenses', 'approve_expenses',
            
            // Data management
            'view_customers', 'create_customers', 'edit_customers',
            'view_suppliers', 'create_suppliers', 'edit_suppliers',
            
            // Financial reports
            'view_financial_reports', 'export_reports',
            'view_balance_sheet', 'view_cash_flow', 'view_trial_balance',
            'view_aging_reports', 'view_tax_reports',
            
            // Multi-currency
            'view_currencies', 'manage_currencies', 'update_exchange_rates',
            
            // Limited settings
            'view_settings'
        ]);
        
        // User role - Basic operations
        $userRole = Role::create(['name' => 'user']);
        $userRole->givePermissionTo([
            // Dashboard
            'view_dashboard',
            
            // Basic invoice operations
            'view_invoices', 'create_invoices', 'edit_invoices',
            
            // Basic expense operations
            'view_expenses', 'create_expenses', 'edit_expenses',
            
            // Customer data
            'view_customers', 'create_customers', 'edit_customers',
            
            // Basic reports
            'view_financial_reports'
        ]);
        
        // Viewer role - Read-only access
        $viewerRole = Role::create(['name' => 'viewer']);
        $viewerRole->givePermissionTo([
            'view_dashboard',
            'view_invoices',
            'view_payments',
            'view_expenses',
            'view_customers',
            'view_suppliers',
            'view_financial_reports',
            'view_currencies'
        ]);
    }
    
    private function createUsers()
    {
        echo "Creating users...\n";
        
        // Admin user
        $admin = User::firstOrCreate(
            ['email' => 'admin@company.com'],
            [
                'name' => 'System Administrator',
                'password' => Hash::make('password123'),
                'email_verified_at' => now(),
            ]
        );
        $admin->assignRole('admin');
        
        // Create profile for admin
        UserProfile::firstOrCreate(
            ['user_id' => $admin->id],
            [
                'phone' => '+1-555-0100',
                'address' => '123 Admin Street, Business City, BC 12345',
                'position' => 'System Administrator',
            ]
        );
        
        // Company settings
        CompanySetting::firstOrCreate(
            ['user_id' => $admin->id],
            [
                'company_name' => 'Sample Company Ltd.',
                'company_email' => 'info@samplecompany.com',
                'company_phone' => '+1-555-0100',
                'company_address' => '123 Business Street, Business City, BC 12345',
                'tax_id' => 'TAX-123456789',
                'base_currency' => 'USD',
                'date_format' => 'Y-m-d',
                'timezone' => 'UTC',
            ]
        );
        
        // Accountant user
        $accountant = User::firstOrCreate(
            ['email' => 'accountant@company.com'],
            [
                'name' => 'Jane Accountant',
                'password' => Hash::make('password123'),
                'email_verified_at' => now(),
            ]
        );
        $accountant->assignRole('accountant');
        
        // Create profile for accountant
        UserProfile::firstOrCreate(
            ['user_id' => $accountant->id],
            [
                'phone' => '+1-555-0101',
                'address' => '456 Finance Avenue, Business City, BC 12345',
                'position' => 'Senior Accountant',
            ]
        );
        
        // Regular user
        $user = User::firstOrCreate(
            ['email' => 'user@company.com'],
            [
                'name' => 'John User',
                'password' => Hash::make('password123'),
                'email_verified_at' => now(),
            ]
        );
        $user->assignRole('user');
        
        // Create profile for user
        UserProfile::firstOrCreate(
            ['user_id' => $user->id],
            [
                'phone' => '+1-555-0102',
                'address' => '789 Employee Lane, Business City, BC 12345',
                'position' => 'Business Analyst',
            ]
        );
        
        // Viewer user (read-only)
        $viewer = User::firstOrCreate(
            ['email' => 'viewer@company.com'],
            [
                'name' => 'Sarah Viewer',
                'password' => Hash::make('password123'),
                'email_verified_at' => now(),
            ]
        );
        $viewer->assignRole('viewer');
        
        echo "Users created with roles:\n";
        echo "  Admin: admin@company.com (password: password123)\n";
        echo "  Accountant: accountant@company.com (password: password123)\n";
        echo "  User: user@company.com (password: password123)\n";
        echo "  Viewer: viewer@company.com (password: password123)\n";
    }
    
    private function createExpenseCategories()
    {
        echo "Creating expense categories...\n";
        
        $categories = [
            ['name' => 'Office Supplies', 'description' => 'Office materials and supplies'],
            ['name' => 'Rent', 'description' => 'Office rent payments'],
            ['name' => 'Utilities', 'description' => 'Electricity, water, gas'],
            ['name' => 'Equipment', 'description' => 'Office equipment purchases'],
            ['name' => 'Computer Equipment', 'description' => 'Computers, laptops, software'],
            ['name' => 'Marketing', 'description' => 'Advertising and marketing expenses'],
            ['name' => 'Insurance', 'description' => 'Business insurance'],
            ['name' => 'Professional Fees', 'description' => 'Legal, accounting, consulting'],
            ['name' => 'Travel', 'description' => 'Business travel expenses'],
            ['name' => 'Meals', 'description' => 'Business meals and entertainment'],
            ['name' => 'Telephone', 'description' => 'Phone and communication expenses'],
            ['name' => 'Internet', 'description' => 'Internet and online services'],
            ['name' => 'Bank Charges', 'description' => 'Bank fees and charges'],
            ['name' => 'Software Licenses', 'description' => 'Software subscriptions and licenses'],
            ['name' => 'Maintenance', 'description' => 'Equipment maintenance and repairs'],
        ];
        
        foreach ($categories as $cat) {
            ExpenseCategory::firstOrCreate($cat, $cat);
        }
        
        echo "Expense categories created.\n";
    }
    
    private function createSampleData()
    {
        echo "Creating sample customers, suppliers, and transactions...\n";
        
        // Create sample customers
        $customers = [
            [
                'name' => 'Acme Corporation',
                'email' => 'billing@acme.com',
                'phone' => '555-0123',
                'address' => '100 Industrial Blvd, Manufacturing City, MC 54321'
            ],
            [
                'name' => 'TechStart Inc',
                'email' => 'finance@techstart.com',
                'phone' => '555-0124',
                'address' => '200 Innovation Dr, Tech Valley, TV 12345'
            ],
            [
                'name' => 'Global Solutions Ltd',
                'email' => 'accounts@global.com',
                'phone' => '555-0125',
                'address' => '300 Commerce St, Business District, BD 67890'
            ],
            [
                'name' => 'Innovation Labs',
                'email' => 'payables@innovation.com',
                'phone' => '555-0126',
                'address' => '400 Research Ave, Science Park, SP 11111'
            ],
            [
                'name' => 'Digital Agency',
                'email' => 'billing@digital.com',
                'phone' => '555-0127',
                'address' => '500 Creative Way, Design District, DD 22222'
            ],
            [
                'name' => 'Metro Enterprises',
                'email' => 'accounting@metro.com',
                'phone' => '555-0128',
                'address' => '600 Urban Center, Metropolitan City, MC 33333'
            ],
        ];
        
        foreach ($customers as $customerData) {
            Customer::firstOrCreate(
                ['email' => $customerData['email']], 
                $customerData
            );
        }
        
        // Create sample suppliers
        $suppliers = [
            ['name' => 'Office Depot', 'email' => 'sales@officedepot.com', 'phone' => '555-1001'],
            ['name' => 'Tech Supplier Inc', 'email' => 'orders@techsupplier.com', 'phone' => '555-1002'],
            ['name' => 'Marketing Agency', 'email' => 'hello@marketing.com', 'phone' => '555-1003'],
            ['name' => 'Legal Services LLC', 'email' => 'billing@legal.com', 'phone' => '555-1004'],
            ['name' => 'Accounting Firm', 'email' => 'services@accounting.com', 'phone' => '555-1005'],
        ];
        
        foreach ($suppliers as $supplierData) {
            Supplier::firstOrCreate(
                ['email' => $supplierData['email']], 
                $supplierData
            );
        }
        
        echo "Creating invoices and payments...\n";
        
        // Create sample invoices
        $customers = Customer::all();
        $users = User::all();
        $startDate = Carbon::now()->subMonths(4);
        
        // Paid invoices (15 invoices)
        for ($i = 0; $i < 15; $i++) {
            $customer = $customers->random();
            $user = $users->random();
            $invoiceDate = $startDate->copy()->addDays(rand(0, 90));
            $dueDate = $invoiceDate->copy()->addDays(30);
            
            $total = rand(1500, 8000);
            
            $invoice = Invoice::create([
                'customer_id' => $customer->id,
                'created_by' => $user->id,
                'invoice_number' => 'INV-' . str_pad($i + 1, 5, '0', STR_PAD_LEFT),
                'status' => 'paid',
                'issue_date' => $invoiceDate,
                'due_date' => $dueDate,
                'subtotal' => $total,
                'tax_amount' => $total * 0.1, // 10% tax
                'discount_amount' => 0,
                'total_amount' => $total * 1.1,
                'paid_amount' => $total * 1.1,
                'balance_amount' => 0,
                'notes' => 'Sample invoice for testing financial reports',
                'currency_code' => 'USD',
                'exchange_rate' => 1.000000,
                'base_currency_amount' => $total * 1.1,
            ]);
            
            // Add items to invoice
            $services = ['Consulting Services', 'Professional Services', 'Software Development', 'Design Services', 'Technical Support'];
            $service = $services[array_rand($services)];
            $quantity = rand(10, 50);
            $unitPrice = rand(50, 200);
            
            InvoiceItem::create([
                'invoice_id' => $invoice->id,
                'description' => $service,
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'total_price' => $quantity * $unitPrice,
            ]);
            
            // Create payment for paid invoices
            $paymentDate = $invoiceDate->copy()->addDays(rand(5, 25));
            Payment::create([
                'invoice_id' => $invoice->id,
                'created_by' => $user->id,
                'amount' => $invoice->total_amount,
                'payment_date' => $paymentDate,
                'payment_method' => ['bank_transfer', 'credit_card', 'check'][array_rand(['bank_transfer', 'credit_card', 'check'])],
                'reference_number' => 'PAY-' . str_pad($i + 1, 5, '0', STR_PAD_LEFT),
                'notes' => 'Payment for invoice ' . $invoice->invoice_number,
                'currency_code' => 'USD',
                'exchange_rate' => 1.000000,
                'base_currency_amount' => $invoice->total_amount,
            ]);
        }
        
        // Unpaid invoices (8 invoices for aging report)
        for ($i = 0; $i < 8; $i++) {
            $customer = $customers->random();
            $user = $users->random();
            $invoiceDate = $startDate->copy()->addDays(rand(45, 120));
            $dueDate = $invoiceDate->copy()->addDays(30);
            $total = rand(1000, 4000);
            
            $invoice = Invoice::create([
                'customer_id' => $customer->id,
                'created_by' => $user->id,
                'invoice_number' => 'INV-' . str_pad(16 + $i, 5, '0', STR_PAD_LEFT),
                'status' => 'sent',
                'issue_date' => $invoiceDate,
                'due_date' => $dueDate,
                'subtotal' => $total,
                'tax_amount' => $total * 0.1,
                'discount_amount' => 0,
                'total_amount' => $total * 1.1,
                'paid_amount' => 0,
                'balance_amount' => $total * 1.1,
                'notes' => 'Unpaid invoice for aging report testing',
                'currency_code' => 'USD',
                'exchange_rate' => 1.000000,
                'base_currency_amount' => $total * 1.1,
            ]);
            
            $services = ['Consulting Services', 'Professional Services', 'Software Development', 'Design Services'];
            $service = $services[array_rand($services)];
            $quantity = rand(8, 30);
            $unitPrice = rand(60, 180);
            
            InvoiceItem::create([
                'invoice_id' => $invoice->id,
                'description' => $service,
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'total_price' => $quantity * $unitPrice,
            ]);
        }
        
        echo "Creating expenses...\n";
        
        // Create sample expenses
        $categories = ExpenseCategory::all();
        $suppliers = Supplier::all();
        
        for ($i = 0; $i < 30; $i++) {
            $category = $categories->random();
            $user = $users->random();
            $supplier = $suppliers->random();
            $expenseDate = $startDate->copy()->addDays(rand(0, 120));
            
            $amount = rand(150, 2500);
            $isDeductible = !in_array($category->name, ['Travel', 'Meals']); // Some expenses are not deductible
            
            $vendors = [
                'Amazon Business', 'Staples', 'Best Buy Business', 'FedEx Office',
                'Verizon Business', 'AT&T Business', 'Local Coffee Shop',
                'Airport Services', 'Hotel Booking', 'Restaurant'
            ];
            $vendor = $vendors[array_rand($vendors)];
            
            Expense::create([
                'expense_category_id' => $category->id,
                'created_by' => $user->id,
                'supplier_id' => $supplier->id,
                'amount' => $amount,
                'expense_date' => $expenseDate,
                'description' => "{$category->name} - {$vendor}",
                'payment_method' => rand(0, 1) ? 'credit_card' : 'bank_transfer',
                'vendor' => $vendor,
                'reference_number' => 'EXP-' . str_pad($i + 1, 5, '0', STR_PAD_LEFT),
                'is_tax_deductible' => $isDeductible,
                'tax_amount' => $isDeductible ? $amount * 0.1 : 0,
                'status' => 'approved',
                'currency_code' => 'USD',
                'exchange_rate' => 1.000000,
                'base_currency_amount' => $amount,
            ]);
        }
        
        // Create some equipment purchases (for investing activities in cash flow)
        $equipmentCategories = ['Equipment', 'Computer Equipment', 'Software Licenses'];
        foreach ($equipmentCategories as $eqCat) {
            $category = ExpenseCategory::where('name', $eqCat)->first();
            if ($category) {
                $user = $users->random();
                $expenseDate = Carbon::now()->subDays(rand(20, 60));
                
                Expense::create([
                    'expense_category_id' => $category->id,
                    'created_by' => $user->id,
                    'amount' => rand(2500, 8000),
                    'expense_date' => $expenseDate,
                    'description' => "Capital Equipment Purchase: {$eqCat}",
                    'payment_method' => 'bank_transfer',
                    'vendor' => 'Equipment Supplier Inc',
                    'reference_number' => 'EQUIP-' . str_replace(' ', '', $eqCat),
                    'is_tax_deductible' => false, // Equipment is depreciated, not expensed
                    'tax_amount' => 0,
                    'status' => 'approved',
                    'currency_code' => 'USD',
                    'exchange_rate' => 1.000000,
                    'base_currency_amount' => rand(2500, 8000),
                ]);
            }
        }
    

        
        // Create expense categories first
        $categories = [
            ['name' => 'Office Supplies', 'description' => 'Office materials and supplies'],
            ['name' => 'Rent', 'description' => 'Office rent payments'],
            ['name' => 'Utilities', 'description' => 'Electricity, water, gas'],
            ['name' => 'Equipment', 'description' => 'Office equipment purchases'],
            ['name' => 'Computer Equipment', 'description' => 'Computers, laptops, software'],
            ['name' => 'Marketing', 'description' => 'Advertising and marketing expenses'],
            ['name' => 'Insurance', 'description' => 'Business insurance'],
            ['name' => 'Professional Fees', 'description' => 'Legal, accounting, consulting'],
            ['name' => 'Travel', 'description' => 'Business travel expenses'],
            ['name' => 'Bank Charges', 'description' => 'Bank fees and charges'],
        ];
        
        foreach ($categories as $cat) {
            ExpenseCategory::firstOrCreate($cat, $cat);
        }
        
        // Create sample customers
        $customers = [
            ['name' => 'Acme Corporation', 'email' => 'billing@acme.com', 'phone' => '555-0123'],
            ['name' => 'TechStart Inc', 'email' => 'finance@techstart.com', 'phone' => '555-0124'],
            ['name' => 'Global Solutions Ltd', 'email' => 'accounts@global.com', 'phone' => '555-0125'],
            ['name' => 'Innovation Labs', 'email' => 'payables@innovation.com', 'phone' => '555-0126'],
            ['name' => 'Digital Agency', 'email' => 'billing@digital.com', 'phone' => '555-0127'],
        ];
        
        foreach ($customers as $customerData) {
            Customer::firstOrCreate(
                ['email' => $customerData['email']], 
                $customerData
            );
        }
        
        // Create sample suppliers
        $suppliers = [
            ['name' => 'Office Depot', 'email' => 'sales@officedepot.com'],
            ['name' => 'Tech Supplier Inc', 'email' => 'orders@techsupplier.com'],
            ['name' => 'Marketing Agency', 'email' => 'hello@marketing.com'],
        ];
        
        foreach ($suppliers as $supplierData) {
            Supplier::firstOrCreate(
                ['email' => $supplierData['email']], 
                $supplierData
            );
        }
        
        echo "Creating invoices and payments...\n";
        
        // Create sample invoices
        $customers = Customer::all();
        $startDate = Carbon::now()->subMonths(3);
        
        // Paid invoices
        for ($i = 0; $i < 15; $i++) {
            $customer = $customers->random();
            $invoiceDate = $startDate->copy()->addDays(rand(0, 60));
            $dueDate = $invoiceDate->copy()->addDays(30);
            
            $invoice = Invoice::create([
                'customer_id' => $customer->id,
                'invoice_number' => 'INV-' . str_pad($i + 1, 5, '0', STR_PAD_LEFT),
                'status' => 'paid',
                'issue_date' => $invoiceDate,
                'due_date' => $dueDate,
                'subtotal' => rand(1000, 5000),
                'tax_amount' => 0, // No tax for simplicity
                'discount_amount' => 0,
                'total_amount' => rand(1000, 5000),
                'paid_amount' => rand(1000, 5000),
                'balance_amount' => 0,
                'notes' => 'Sample invoice for testing',
            ]);
            
            // Add items to invoice
            InvoiceItem::create([
                'invoice_id' => $invoice->id,
                'description' => 'Professional Services',
                'quantity' => rand(10, 40),
                'unit_price' => rand(50, 200),
                'total_price' => rand(1000, 5000),
            ]);
            
            // Create payment for paid invoices
            Payment::create([
                'invoice_id' => $invoice->id,
                'amount' => $invoice->total_amount,
                'payment_date' => $invoiceDate->copy()->addDays(rand(5, 25)),
                'payment_method' => 'bank_transfer',
                'reference_number' => 'PAY-' . str_pad($i + 1, 5, '0', STR_PAD_LEFT),
            ]);
        }
        
        // Unpaid invoices (for aging report)
        for ($i = 0; $i < 8; $i++) {
            $customer = $customers->random();
            $invoiceDate = $startDate->copy()->addDays(rand(30, 90));
            $dueDate = $invoiceDate->copy()->addDays(30);
            $total = rand(800, 3000);
            
            $invoice = Invoice::create([
                'customer_id' => $customer->id,
                'invoice_number' => 'INV-' . str_pad(16 + $i, 5, '0', STR_PAD_LEFT),
                'status' => 'sent',
                'issue_date' => $invoiceDate,
                'due_date' => $dueDate,
                'subtotal' => $total,
                'tax_amount' => 0,
                'discount_amount' => 0,
                'total_amount' => $total,
                'paid_amount' => 0,
                'balance_amount' => $total,
                'notes' => 'Unpaid invoice for aging report testing',
            ]);
            
            InvoiceItem::create([
                'invoice_id' => $invoice->id,
                'description' => 'Consulting Services',
                'quantity' => rand(5, 25),
                'unit_price' => rand(60, 180),
                'total_price' => $total,
            ]);
        }
        
        echo "Creating expenses...\n";
        
        // Create sample expenses
        $categories = ExpenseCategory::all();
        
        for ($i = 0; $i < 25; $i++) {
            $category = $categories->random();
            $expenseDate = $startDate->copy()->addDays(rand(0, 90));
            
            $amount = rand(100, 2000);
            $isDeductible = !in_array($category->name, ['Travel', 'Meals']); // Some expenses are not deductible
            
            Expense::create([
                'expense_category_id' => $category->id,
                'amount' => $amount,
                'expense_date' => $expenseDate,
                'description' => "Sample expense: {$category->name}",
                'payment_method' => rand(0, 1) ? 'credit_card' : 'bank_transfer',
                'vendor' => "Vendor " . ($i + 1),
                'reference_number' => 'EXP-' . str_pad($i + 1, 5, '0', STR_PAD_LEFT),
                'is_tax_deductible' => $isDeductible,
                'tax_amount' => $isDeductible ? $amount * 0.1 : 0,
                'status' => 'approved',
            ]);
        }
        
        // Create some equipment purchases (for investing activities)
        $equipmentCategories = ['Equipment', 'Computer Equipment'];
        foreach ($equipmentCategories as $eqCat) {
            $category = ExpenseCategory::where('name', $eqCat)->first();
            if ($category) {
                Expense::create([
                    'expense_category_id' => $category->id,
                    'amount' => rand(2000, 5000),
                    'expense_date' => Carbon::now()->subDays(rand(15, 45)),
                    'description' => "Equipment purchase: {$eqCat}",
                    'payment_method' => 'bank_transfer',
                    'vendor' => 'Equipment Supplier',
                    'reference_number' => 'EQUIP-' . $eqCat,
                    'is_tax_deductible' => false, // Equipment is depreciated, not expensed
                    'tax_amount' => 0,
                    'status' => 'approved',
                ]);
            }
        }
        
        echo "Sample data created successfully!\n";
        echo "You now have:\n";
        echo "- " . Invoice::count() . " invoices (" . Invoice::where('status', 'paid')->count() . " paid, " . Invoice::where('status', '!=', 'paid')->count() . " unpaid)\n";
        echo "- " . Payment::count() . " payments\n";
        echo "- " . Expense::count() . " expenses\n";
        echo "- " . Customer::count() . " customers\n";
        echo "- " . ExpenseCategory::count() . " expense categories\n\n";
        echo "You can now test the financial reports!\n";
    }
}