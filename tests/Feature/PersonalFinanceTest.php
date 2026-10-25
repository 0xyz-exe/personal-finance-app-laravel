<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PersonalFinanceTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_view_dashboard(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('dashboard'));
        $response->assertStatus(200);
        $response->assertSee('Financial Overview');
    }

    public function test_user_can_create_wallet(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('wallets.store'), [
            'name' => 'Credit Card',
            'type' => 'credit_card',
            'initial_balance' => 0.00,
            'currency' => 'USD',
        ]);

        $response->assertRedirect(route('wallets.index'));
        $this->assertDatabaseHas('wallets', [
            'user_id' => $user->id,
            'name' => 'Credit Card',
            'type' => 'credit_card',
        ]);
    }

    public function test_user_can_create_category_with_budget(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('categories.store'), [
            'name' => 'Health & Medical',
            'type' => 'expense',
            'budget_limit' => 250.00,
        ]);

        $response->assertRedirect(route('categories.index'));
        $this->assertDatabaseHas('categories', [
            'user_id' => $user->id,
            'name' => 'Health & Medical',
            'budget_limit' => 250.00,
        ]);
    }

    public function test_user_can_record_transaction_and_balance_updates(): void
    {
        $user = User::factory()->create();
        $wallet = Wallet::create([
            'user_id' => $user->id,
            'name' => 'Bank Checking',
            'type' => 'bank',
            'initial_balance' => 1000.00,
            'currency' => 'USD',
        ]);
        $category = Category::create([
            'user_id' => $user->id,
            'name' => 'Utilities',
            'type' => 'expense',
            'budget_limit' => 300.00,
        ]);

        // Add expense
        $response = $this->actingAs($user)->post(route('transactions.store'), [
            'wallet_id' => $wallet->id,
            'category_id' => $category->id,
            'type' => 'expense',
            'amount' => 150.00,
            'transaction_date' => now()->toDateString(),
            'title' => 'Electric bill',
        ]);

        $response->assertRedirect(route('transactions.index'));
        $this->assertDatabaseHas('transactions', [
            'user_id' => $user->id,
            'amount' => 150.00,
            'title' => 'Electric bill',
        ]);

        // Wallet balance calculation: 1000 - 150 = 850
        $this->assertEquals(850.00, $wallet->balance);

        // Category monthly spent: 150
        $this->assertEquals(150.00, $category->monthly_spent);
        $this->assertEquals(50.0, $category->budget_usage_percent);
    }

    public function test_user_can_delete_transaction(): void
    {
        $user = User::factory()->create();
        $wallet = Wallet::create([
            'user_id' => $user->id,
            'name' => 'Cash',
            'type' => 'cash',
            'initial_balance' => 200.00,
            'currency' => 'USD',
        ]);
        $category = Category::create([
            'user_id' => $user->id,
            'name' => 'Food',
            'type' => 'expense',
        ]);

        $tx = Transaction::create([
            'user_id' => $user->id,
            'wallet_id' => $wallet->id,
            'category_id' => $category->id,
            'type' => 'expense',
            'amount' => 50.00,
            'transaction_date' => now()->toDateString(),
            'title' => 'Lunch',
        ]);

        $this->actingAs($user)->delete(route('transactions.destroy', $tx))
            ->assertRedirect(route('transactions.index'));

        $this->assertDatabaseMissing('transactions', ['id' => $tx->id]);
        $this->assertEquals(200.00, $wallet->balance);
    }
}
