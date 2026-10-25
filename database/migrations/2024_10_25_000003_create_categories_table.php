<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('name'); // Food & Dining, Salary, Rent, Entertainment
            $table->enum('type', ['income', 'expense']);
            $table->string('color', 20)->default('#10b981');
            $table->string('icon', 50)->default('tag');
            $table->decimal('budget_limit', 14, 2)->nullable(); // monthly budget limit
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
