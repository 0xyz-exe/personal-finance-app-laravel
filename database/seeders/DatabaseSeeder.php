<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Users
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'is_admin' => true,
        ]);

        $testUser = User::create([
            'name' => 'Jordan Reed',
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
            'is_admin' => false,
        ]);

        // 2. Wallets for testUser
        $walletChecking = Wallet::create([
            'user_id' => $testUser->id,
            'name' => 'Primary Checking',
            'type' => 'bank',
            'initial_balance' => 3200.00,
            'currency' => 'USD',
            'color' => '#2563eb',
        ]);

        $walletSavings = Wallet::create([
            'user_id' => $testUser->id,
            'name' => 'High-Yield Savings',
            'type' => 'savings',
            'initial_balance' => 15000.00,
            'currency' => 'USD',
            'color' => '#059669',
        ]);

        $walletCash = Wallet::create([
            'user_id' => $testUser->id,
            'name' => 'Cash Wallet',
            'type' => 'cash',
            'initial_balance' => 250.00,
            'currency' => 'USD',
            'color' => '#d97706',
        ]);

        // 3. Categories
        $catSalary = Category::create([
            'user_id' => $testUser->id,
            'name' => 'Salary & Wages',
            'type' => 'income',
            'color' => '#10b981',
        ]);

        $catFreelance = Category::create([
            'user_id' => $testUser->id,
            'name' => 'Freelance / Consulting',
            'type' => 'income',
            'color' => '#06b6d4',
        ]);

        $catGroceries = Category::create([
            'user_id' => $testUser->id,
            'name' => 'Groceries & Food',
            'type' => 'expense',
            'color' => '#f59e0b',
            'budget_limit' => 600.00,
        ]);

        $catDining = Category::create([
            'user_id' => $testUser->id,
            'name' => 'Restaurants & Bars',
            'type' => 'expense',
            'color' => '#ef4444',
            'budget_limit' => 300.00,
        ]);

        $catRent = Category::create([
            'user_id' => $testUser->id,
            'name' => 'Housing & Rent',
            'type' => 'expense',
            'color' => '#8b5cf6',
            'budget_limit' => 1400.00,
        ]);

        $catUtilities = Category::create([
            'user_id' => $testUser->id,
            'name' => 'Utilities & Internet',
            'type' => 'expense',
            'color' => '#6b7280',
            'budget_limit' => 200.00,
        ]);

        // 4. Seed Transactions
        // Salary
        Transaction::create([
            'user_id' => $testUser->id,
            'wallet_id' => $walletChecking->id,
            'category_id' => $catSalary->id,
            'type' => 'income',
            'amount' => 4500.00,
            'transaction_date' => now()->startOfMonth()->addDays(1),
            'title' => 'Monthly Tech Payroll Deposit',
        ]);

        // Freelance
        Transaction::create([
            'user_id' => $testUser->id,
            'wallet_id' => $walletChecking->id,
            'category_id' => $catFreelance->id,
            'type' => 'income',
            'amount' => 850.00,
            'transaction_date' => now()->startOfMonth()->addDays(5),
            'title' => 'Frontend Web Audit Milestone',
        ]);

        // Rent
        Transaction::create([
            'user_id' => $testUser->id,
            'wallet_id' => $walletChecking->id,
            'category_id' => $catRent->id,
            'type' => 'expense',
            'amount' => 1350.00,
            'transaction_date' => now()->startOfMonth()->addDays(2),
            'title' => 'Apartment Monthly Rent',
        ]);

        // Groceries
        Transaction::create([
            'user_id' => $testUser->id,
            'wallet_id' => $walletChecking->id,
            'category_id' => $catGroceries->id,
            'type' => 'expense',
            'amount' => 184.50,
            'transaction_date' => now()->subDays(6),
            'title' => 'Supermarket Whole Foods Run',
        ]);

        Transaction::create([
            'user_id' => $testUser->id,
            'wallet_id' => $walletChecking->id,
            'category_id' => $catGroceries->id,
            'type' => 'expense',
            'amount' => 92.20,
            'transaction_date' => now()->subDays(2),
            'title' => 'Farmer Market Fresh Produce',
        ]);

        // Dining out
        Transaction::create([
            'user_id' => $testUser->id,
            'wallet_id' => $walletCash->id,
            'category_id' => $catDining->id,
            'type' => 'expense',
            'amount' => 64.00,
            'transaction_date' => now()->subDays(4),
            'title' => 'Dinner with Colleagues',
        ]);

        // Utilities
        Transaction::create([
            'user_id' => $testUser->id,
            'wallet_id' => $walletChecking->id,
            'category_id' => $catUtilities->id,
            'type' => 'expense',
            'amount' => 85.00,
            'transaction_date' => now()->subDays(8),
            'title' => 'Fiber Internet Bill',
        ]);
    }
}
