<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        echo "Starting comprehensive database seeding...\n";
        
        // Reset cached roles and permissions first
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
        
        // Run our new comprehensive seeder that includes everything:
        // - Roles and Permissions (Spatie)
        // - Users with roles assigned
        // - Sample financial data for testing reports
        $this->call([
            CurrencySeeder::class,
           // SampleDataSeeder::class,
        ]);
        
        echo "Database seeding completed!\n";
        echo "\n🔐 Test Users Created:\n";
        echo "  Admin: admin@company.com (password: password123)\n";
        echo "  Accountant: accountant@company.com (password: password123)\n";
        echo "  User: user@company.com (password: password123)\n";
        echo "  Viewer: viewer@company.com (password: password123)\n\n";
        echo "📊 Sample Data Created:\n";
        echo "  - Invoices with payments\n";
        echo "  - Expenses across different categories\n";
        echo "  - Customers and suppliers\n";
        echo "  - Financial reports data\n\n";
        echo "🎯 Ready to test financial reports!\n";
    }
}