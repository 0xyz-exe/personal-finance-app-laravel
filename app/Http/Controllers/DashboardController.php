<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $user = auth()->user();

        $wallets = $user->wallets;
        $totalBalance = $wallets->sum(fn($w) => $w->balance);

        $thisMonthIncome = $user->transactions()
            ->where('type', 'income')
            ->whereYear('transaction_date', now()->year)
            ->whereMonth('transaction_date', now()->month)
            ->sum('amount');

        $thisMonthExpense = $user->transactions()
            ->where('type', 'expense')
            ->whereYear('transaction_date', now()->year)
            ->whereMonth('transaction_date', now()->month)
            ->sum('amount');

        $netCashflow = $thisMonthIncome - $thisMonthExpense;

        $recentTransactions = $user->transactions()
            ->with(['wallet', 'category'])
            ->latest('transaction_date')
            ->take(6)
            ->get();

        $budgetAlerts = Category::where(function ($q) use ($user) {
                $q->where('user_id', $user->id)->orWhereNull('user_id');
            })
            ->whereNotNull('budget_limit')
            ->where('budget_limit', '>', 0)
            ->get()
            ->filter(fn($c) => $c->monthly_spent > 0)
            ->sortByDesc('budget_usage_percent')
            ->take(4);

        return view('dashboard', compact(
            'wallets',
            'totalBalance',
            'thisMonthIncome',
            'thisMonthExpense',
            'netCashflow',
            'recentTransactions',
            'budgetAlerts'
        ));
    }
}
