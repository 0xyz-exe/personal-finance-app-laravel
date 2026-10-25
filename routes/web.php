<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\WalletController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Wallets / Accounts
    Route::resource('wallets', WalletController::class)->only(['index', 'store', 'update', 'destroy']);

    // Categories & Budget Limits
    Route::resource('categories', CategoryController::class)->only(['index', 'store', 'update', 'destroy']);

    // Transactions
    Route::resource('transactions', TransactionController::class);
});

require __DIR__.'/auth.php';
