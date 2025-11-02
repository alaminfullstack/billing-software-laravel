<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Category;
use App\Models\ExpenseCategory;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Service;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Payment;
use App\Models\Expense;
use App\Models\UserProfile;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // Create default user
        $user = User::create([
            'name' => 'John Doe',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
        ]);

        UserProfile::create([
            'user_id' => $user->id,
            'company_name' => 'Acme Corporation',
            'phone' => '+1-555-0123',
            'tax_number' => 'TAX123456789',
            'address' => '123 Business Street',
            'city' => 'New York',
            'state' => 'NY',
            'country' => 'USA',
            'zip_code' => '10001',
        ]);

        $user->assignRole('admin');

        // Create Categories
        $productCategories = [
            ['name' => 'Electronics', 'description' => 'Electronic devices and components', 'color' => '#3B82F6', 'icon' => 'fas fa-laptop'],
            ['name' => 'Office Supplies', 'description' => 'Office stationery and supplies', 'color' => '#10B981', 'icon' => 'fas fa-pen'],
            ['name' => 'Furniture', 'description' => 'Office and home furniture', 'color' => '#F59E0B', 'icon' => 'fas fa-chair'],
            ['name' => 'Software', 'description' => 'Software licenses and subscriptions', 'color' => '#8B5CF6', 'icon' => 'fas fa-code'],
        ];

        foreach ($productCategories as $categoryData) {
            Category::create(array_merge($categoryData, ['type' => 'product']));
        }

        $serviceCategories = [
            ['name' => 'Consulting', 'description' => 'Business consulting services', 'color' => '#EF4444', 'icon' => 'fas fa-user-tie'],
            ['name' => 'Development', 'description' => 'Software development services', 'color' => '#06B6D4', 'icon' => 'fas fa-cogs'],
            ['name' => 'Design', 'description' => 'Graphic and web design services', 'color' => '#EC4899', 'icon' => 'fas fa-palette'],
            ['name' => 'Marketing', 'description' => 'Digital marketing services', 'color' => '#84CC16', 'icon' => 'fas fa-bullhorn'],
        ];

        foreach ($serviceCategories as $categoryData) {
            Category::create(array_merge($categoryData, ['type' => 'service']));
        }

        // Create Expense Categories
        $expenseCategories = [
            ['name' => 'Office Rent', 'description' => 'Monthly office rent and utilities'],
            ['name' => 'Software Subscriptions', 'description' => 'Monthly software license fees'],
            ['name' => 'Marketing & Advertising', 'description' => 'Marketing campaigns and ads'],
            ['name' => 'Travel & Transportation', 'description' => 'Business travel expenses'],
            ['name' => 'Professional Services', 'description' => 'Legal, accounting, and consulting fees'],
            ['name' => 'Equipment', 'description' => 'Office equipment and technology'],
            ['name' => 'Training & Education', 'description' => 'Employee training and courses'],
            ['name' => 'Insurance', 'description' => 'Business insurance premiums'],
        ];

        foreach ($expenseCategories as $categoryData) {
            ExpenseCategory::create($categoryData);
        }

        // Create Customers
        $customers = [
            [
                'name' => 'Tech Solutions Inc.',
                'email' => 'contact@techsolutions.com',
                'phone' => '+1-555-0101',
                'address' => '456 Tech Avenue',
                'city' => 'San Francisco',
                'state' => 'CA',
                'country' => 'USA',
                'zip_code' => '94102',
                'customer_type' => 'business',
                'status' => 'active',
                'notes' => 'Premium client - Net 30 terms',
            ],
            [
                'name' => 'Global Enterprises',
                'email' => 'billing@globalent.com',
                'phone' => '+1-555-0102',
                'address' => '789 Commerce Blvd',
                'city' => 'Chicago',
                'state' => 'IL',
                'country' => 'USA',
                'zip_code' => '60601',
                'customer_type' => 'business',
                'status' => 'active',
                'notes' => 'Large enterprise client',
            ],
            [
                'name' => 'Sarah Johnson',
                'email' => 'sarah.j@email.com',
                'phone' => '+1-555-0103',
                'address' => '321 Main Street',
                'city' => 'Austin',
                'state' => 'TX',
                'country' => 'USA',
                'zip_code' => '73301',
                'customer_type' => 'individual',
                'status' => 'active',
                'notes' => 'Freelance consultant',
            ],
            [
                'name' => 'StartupXYZ',
                'email' => 'info@startupxyz.com',
                'phone' => '+1-555-0104',
                'address' => '555 Innovation Drive',
                'city' => 'Seattle',
                'state' => 'WA',
                'country' => 'USA',
                'zip_code' => '98101',
                'customer_type' => 'business',
                'status' => 'active',
                'notes' => 'Growing startup',
            ],
            [
                'name' => 'Michael Chen',
                'email' => 'm.chen@email.com',
                'phone' => '+1-555-0105',
                'address' => '789 Oak Avenue',
                'city' => 'Boston',
                'state' => 'MA',
                'country' => 'USA',
                'zip_code' => '02101',
                'customer_type' => 'individual',
                'status' => 'active',
                'notes' => 'Regular client',
            ],
        ];

        foreach ($customers as $customerData) {
            Customer::create(array_merge($customerData, ['user_id' => $user->id]));
        }

        // Create Products
        $products = [
            [
                'name' => 'Laptop - Business Grade',
                'description' => 'High-performance laptop for business use',
                'sku' => 'LAP-001',
                'barcode' => '123456789012',
                'unit_price' => 1299.99,
                'cost_price' => 899.99,
                'selling_price' => 1299.99,
                'category_id' => 1,
                'stock_quantity' => 25,
                'min_stock_level' => 5,
                'reorder_level' => 10,
                'low_stock_alert' => 5,
                'unit' => 'piece',
            ],
            [
                'name' => 'Wireless Mouse',
                'description' => 'Ergonomic wireless mouse',
                'sku' => 'MOUSE-001',
                'barcode' => '123456789013',
                'unit_price' => 29.99,
                'cost_price' => 15.99,
                'selling_price' => 29.99,
                'category_id' => 1,
                'stock_quantity' => 150,
                'min_stock_level' => 20,
                'reorder_level' => 50,
                'low_stock_alert' => 20,
                'unit' => 'piece',
            ],
            [
                'name' => 'Office Chair',
                'description' => 'Ergonomic office chair with lumbar support',
                'sku' => 'CHAIR-001',
                'barcode' => '123456789014',
                'unit_price' => 299.99,
                'cost_price' => 199.99,
                'selling_price' => 299.99,
                'category_id' => 3,
                'stock_quantity' => 15,
                'min_stock_level' => 3,
                'reorder_level' => 8,
                'low_stock_alert' => 3,
                'unit' => 'piece',
            ],
            [
                'name' => 'Notebook A4',
                'description' => 'Professional notebook for meetings',
                'sku' => 'NOTE-001',
                'barcode' => '123456789015',
                'unit_price' => 4.99,
                'cost_price' => 2.50,
                'selling_price' => 4.99,
                'category_id' => 2,
                'stock_quantity' => 500,
                'min_stock_level' => 100,
                'reorder_level' => 200,
                'low_stock_alert' => 50,
                'unit' => 'piece',
            ],
            [
                'name' => 'Software License - Pro',
                'description' => 'Annual software license for professionals',
                'sku' => 'SOFT-001',
                'barcode' => '123456789016',
                'unit_price' => 599.99,
                'cost_price' => 399.99,
                'selling_price' => 599.99,
                'category_id' => 4,
                'stock_quantity' => 0,
                'min_stock_level' => 0,
                'reorder_level' => 0,
                'low_stock_alert' => 5,
                'track_inventory' => false,
                'unit' => 'license',
            ],
        ];

        foreach ($products as $productData) {
            Product::create($productData);
        }

        // Create Services
        $services = [
            [
                'name' => 'Business Consulting',
                'description' => 'Strategic business consulting services',
                'hourly_rate' => 150.00,
                'duration' => 1,
                'duration_unit' => 'hours',
                'basic_price' => 150.00,
                'standard_price' => 200.00,
                'premium_price' => 300.00,
                'category_id' => 5,
                'features' => ['Strategic Planning', 'Market Analysis', 'Growth Strategies'],
                'is_bookable' => true,
            ],
            [
                'name' => 'Web Development',
                'description' => 'Custom web application development',
                'hourly_rate' => 120.00,
                'duration' => 1,
                'duration_unit' => 'hours',
                'basic_price' => 2000.00,
                'standard_price' => 5000.00,
                'premium_price' => 10000.00,
                'category_id' => 6,
                'features' => ['Responsive Design', 'CMS Integration', 'SEO Optimization'],
                'is_bookable' => true,
            ],
            [
                'name' => 'Graphic Design',
                'description' => 'Professional graphic design services',
                'hourly_rate' => 80.00,
                'duration' => 1,
                'duration_unit' => 'hours',
                'basic_price' => 200.00,
                'standard_price' => 500.00,
                'premium_price' => 1000.00,
                'category_id' => 7,
                'features' => ['Logo Design', 'Brand Identity', 'Marketing Materials'],
                'is_bookable' => true,
            ],
            [
                'name' => 'Digital Marketing',
                'description' => 'Comprehensive digital marketing strategy',
                'hourly_rate' => 100.00,
                'duration' => 1,
                'duration_unit' => 'hours',
                'basic_price' => 1500.00,
                'standard_price' => 3000.00,
                'premium_price' => 5000.00,
                'category_id' => 8,
                'features' => ['Social Media', 'SEO', 'PPC Campaigns', 'Analytics'],
                'is_bookable' => true,
            ],
        ];

        foreach ($services as $serviceData) {
            Service::create($serviceData);
        }

        // Create Invoices
        $invoiceData = [
            [
                'customer_id' => 1,
                'issue_date' => Carbon::now()->subDays(10),
                'due_date' => Carbon::now()->addDays(20),
                'status' => 'paid',
                'invoice_items' => [
                    ['product_id' => 1, 'description' => 'Laptop - Business Grade', 'quantity' => 2, 'unit_price' => 1299.99, 'tax_rate' => 8.25],
                    ['product_id' => 2, 'description' => 'Wireless Mouse', 'quantity' => 2, 'unit_price' => 29.99, 'tax_rate' => 8.25],
                ],
            ],
            [
                'customer_id' => 2,
                'issue_date' => Carbon::now()->subDays(5),
                'due_date' => Carbon::now()->addDays(25),
                'status' => 'sent',
                'invoice_items' => [
                    ['service_id' => 1, 'description' => 'Business Consulting', 'quantity' => 10, 'unit_price' => 150.00, 'tax_rate' => 8.25],
                    ['service_id' => 2, 'description' => 'Web Development', 'quantity' => 1, 'unit_price' => 5000.00, 'tax_rate' => 8.25],
                ],
            ],
            [
                'customer_id' => 3,
                'issue_date' => Carbon::now()->subDays(3),
                'due_date' => Carbon::now()->addDays(27),
                'status' => 'paid',
                'invoice_items' => [
                    ['service_id' => 3, 'description' => 'Graphic Design', 'quantity' => 5, 'unit_price' => 80.00, 'tax_rate' => 8.25],
                ],
            ],
        ];

        foreach ($invoiceData as $data) {
            $invoice = Invoice::create([
                'customer_id' => $data['customer_id'],
                'created_by' => $user->id,
                'invoice_number' => 'INV-' . str_pad(rand(1, 9999), 6, '0', STR_PAD_LEFT),
                'issue_date' => $data['issue_date'],
                'due_date' => $data['due_date'],
                'status' => $data['status'],
                'notes' => 'Thank you for your business!',
            ]);

            $subtotal = 0;
            $taxAmount = 0;

            foreach ($data['invoice_items'] as $item) {
                $itemSubtotal = $item['quantity'] * $item['unit_price'];
                $itemTax = $itemSubtotal * (($item['tax_rate'] ?? 0) / 100);
                
                $subtotal += $itemSubtotal;
                $taxAmount += $itemTax;
                
                InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'product_id' => $item['product_id'] ?? null,
                    'service_id' => $item['service_id'] ?? null,
                    'description' => $item['description'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'tax_rate' => $item['tax_rate'] ?? 0,
                    'tax_amount' => $itemTax,
                    'total_amount' => $itemSubtotal + $itemTax,
                ]);
            }

            $totalAmount = $subtotal + $taxAmount;
            
            $invoice->update([
                'subtotal' => $subtotal,
                'tax_amount' => $taxAmount,
                'total_amount' => $totalAmount,
                'paid_amount' => $data['status'] === 'paid' ? $totalAmount : 0,
                'balance_amount' => $data['status'] === 'paid' ? 0 : $totalAmount,
            ]);

            // Create payments for paid invoices
            if ($data['status'] === 'paid') {
                Payment::create([
                    'invoice_id' => $invoice->id,
                    'created_by' => $user->id,
                    'payment_date' => Carbon::now()->subDays(2),
                    'amount' => $totalAmount,
                    'payment_method' => 'bank_transfer',
                    'reference_number' => 'PAY-' . str_pad(rand(1, 9999), 6, '0', STR_PAD_LEFT),
                ]);
            }
        }

        // Create Expenses
        $expenses = [
            [
                'expense_category_id' => 1,
                'amount' => 2500.00,
                'expense_date' => Carbon::now()->subDays(15),
                'description' => 'Monthly office rent',
                'payment_method' => 'bank_transfer',
                'vendor' => 'Downtown Properties LLC',
                'reference_number' => 'RENT-001',
                'status' => 'approved',
                'is_tax_deductible' => true,
            ],
            [
                'expense_category_id' => 2,
                'amount' => 599.99,
                'expense_date' => Carbon::now()->subDays(10),
                'description' => 'Software license renewal',
                'payment_method' => 'credit_card',
                'vendor' => 'TechSoft Inc.',
                'reference_number' => 'SOFT-001',
                'status' => 'approved',
                'is_tax_deductible' => true,
            ],
            [
                'expense_category_id' => 3,
                'amount' => 1500.00,
                'expense_date' => Carbon::now()->subDays(8),
                'description' => 'Digital advertising campaign',
                'payment_method' => 'online',
                'vendor' => 'AdPlatform Pro',
                'reference_number' => 'ADV-001',
                'status' => 'approved',
                'is_tax_deductible' => true,
            ],
            [
                'expense_category_id' => 4,
                'amount' => 450.00,
                'expense_date' => Carbon::now()->subDays(5),
                'description' => 'Business travel to client meeting',
                'payment_method' => 'credit_card',
                'vendor' => 'Various',
                'reference_number' => 'TRV-001',
                'status' => 'pending',
                'is_tax_deductible' => true,
            ],
            [
                'expense_category_id' => 5,
                'amount' => 800.00,
                'expense_date' => Carbon::now()->subDays(3),
                'description' => 'Legal consultation',
                'payment_method' => 'bank_transfer',
                'vendor' => 'Law Firm Associates',
                'reference_number' => 'LEGAL-001',
                'status' => 'approved',
                'is_tax_deductible' => true,
            ],
        ];

        

        // Create Tax Rates
        $taxes = [
            ['name' => 'Sales Tax', 'code' => 'SALES', 'rate' => 8.25, 'type' => 'percentage', 'description' => 'Standard sales tax rate'],
            ['name' => 'VAT', 'code' => 'VAT', 'rate' => 20.0, 'type' => 'percentage', 'description' => 'Value Added Tax'],
            ['name' => 'Service Tax', 'code' => 'SERVICE', 'rate' => 15.0, 'type' => 'percentage', 'description' => 'Service tax rate'],
            ['name' => 'Local Tax', 'code' => 'LOCAL', 'rate' => 2.5, 'type' => 'percentage', 'description' => 'Local municipality tax'],
        ];

        foreach ($taxes as $taxData) {
            \App\Models\Tax::create($taxData);
        }

        // Create Quotes
        $quotes = [
            [
                'customer_id' => 4,
                'quote_date' => Carbon::now()->subDays(7),
                'valid_until' => Carbon::now()->addDays(14),
                'status' => 'sent',
                'quote_items' => [
                    ['product_id' => 3, 'description' => 'Office Chair', 'quantity' => 5, 'unit_price' => 299.99, 'tax_rate' => 8.25],
                    ['service_id' => 1, 'description' => 'Business Consulting', 'quantity' => 5, 'unit_price' => 150.00, 'tax_rate' => 8.25],
                ],
            ],
            [
                'customer_id' => 5,
                'quote_date' => Carbon::now()->subDays(3),
                'valid_until' => Carbon::now()->addDays(18),
                'status' => 'draft',
                'quote_items' => [
                    ['service_id' => 2, 'description' => 'Web Development', 'quantity' => 1, 'unit_price' => 3000.00, 'tax_rate' => 8.25],
                ],
            ],
        ];

        foreach ($quotes as $quoteData) {
            $quote = \App\Models\Quote::create([
                'customer_id' => $quoteData['customer_id'],
                'created_by' => $user->id,
                'quote_number' => 'QUO-' . str_pad(rand(1, 9999), 6, '0', STR_PAD_LEFT),
                'status' => $quoteData['status'],
                'quote_date' => $quoteData['quote_date'],
                'valid_until' => $quoteData['valid_until'],
                'terms' => 'Payment due within 30 days. Valid for 30 days from quote date.',
                'notes' => 'Thank you for considering our services!',
            ]);

            $subtotal = 0;
            $taxAmount = 0;

            foreach ($quoteData['quote_items'] as $item) {
                $itemSubtotal = $item['quantity'] * $item['unit_price'];
                $itemTax = $itemSubtotal * (($item['tax_rate'] ?? 0) / 100);
                
                $subtotal += $itemSubtotal;
                $taxAmount += $itemTax;
                
                \App\Models\QuoteItem::create([
                    'quote_id' => $quote->id,
                    'product_id' => $item['product_id'] ?? null,
                    'service_id' => $item['service_id'] ?? null,
                    'description' => $item['description'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'tax_rate' => $item['tax_rate'] ?? 0,
                    'tax_amount' => $itemTax,
                    'total_amount' => $itemSubtotal + $itemTax,
                ]);
            }

            $totalAmount = $subtotal + $taxAmount;
            
            $quote->update([
                'subtotal' => $subtotal,
                'tax_amount' => $taxAmount,
                'total_amount' => $totalAmount,
            ]);
        }

        // Create Suppliers
        $suppliers = [
            [
                'name' => 'Tech Supply Co.',
                'email' => 'orders@techsupply.com',
                'phone' => '+1-555-0201',
                'address' => '100 Industrial Ave',
                'city' => 'Dallas',
                'state' => 'TX',
                'country' => 'USA',
                'zip_code' => '75201',
                'tax_number' => 'TAX987654321',
                'website' => 'www.techsupply.com',
                'status' => 'active',
            ],
            [
                'name' => 'Office Essentials Ltd.',
                'email' => 'sales@officeessentials.com',
                'phone' => '+1-555-0202',
                'address' => '200 Business Park',
                'city' => 'Atlanta',
                'state' => 'GA',
                'country' => 'USA',
                'zip_code' => '30309',
                'tax_number' => 'TAX456789123',
                'website' => 'www.officeessentials.com',
                'status' => 'active',
            ],
        ];

        $createdSuppliers = [];
        foreach ($suppliers as $supplierData) {
            $createdSuppliers[] = \App\Models\Supplier::create($supplierData);
        }

        foreach ($expenses as $index => $expenseData) {
            $expenseData['created_by'] = $user->id;
            // Assign some expenses to suppliers
            if (isset($createdSuppliers[$index % count($createdSuppliers)])) {
                $expenseData['supplier_id'] = $createdSuppliers[$index % count($createdSuppliers)]->id;
            }
            Expense::create($expenseData);
        }

        // Create Inventory Transactions
        $inventoryTransactions = [
            [
                'product_id' => 1,
                'type' => 'purchase',
                'quantity' => 50,
                'previous_stock' => 0,
                'new_stock' => 50,
                'unit_cost' => 899.99,
                'notes' => 'Initial stock purchase',
            ],
            [
                'product_id' => 2,
                'type' => 'purchase',
                'quantity' => 200,
                'previous_stock' => 0,
                'new_stock' => 200,
                'unit_cost' => 15.99,
                'notes' => 'Bulk mouse purchase',
            ],
            [
                'product_id' => 1,
                'type' => 'sale',
                'quantity' => 25,
                'previous_stock' => 50,
                'new_stock' => 25,
                'unit_cost' => 899.99,
                'reference_type' => 'Invoice',
                'notes' => 'Sold to customers',
            ],
        ];

        foreach ($inventoryTransactions as $transactionData) {
            $transactionData['user_id'] = $user->id;
            \App\Models\InventoryTransaction::create($transactionData);
        }

        $this->command->info('Database seeded successfully!');
        $this->command->info('Default login: admin@example.com / password');
    }
}
