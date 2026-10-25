<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('wallets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('name'); // e.g. Main Checking, Cash, Savings, Crypto
            $table->enum('type', ['bank', 'cash', 'credit_card', 'savings', 'e_wallet'])->default('bank');
            $table->decimal('initial_balance', 14, 2)->default(0);
            $table->string('currency', 10)->default('USD');
            $table->string('color', 20)->default('#4f46e5');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wallets');
    }
};
