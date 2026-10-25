<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Transaction;
use App\Models\Wallet;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TransactionController extends Controller
{
    public function index(Request $request): View
    {
        $user = auth()->user();
        $query = $user->transactions()->with(['wallet', 'category']);

        if ($request->filled('type')) {
            $query->where('type', $request->input('type'));
        }

        if ($request->filled('wallet_id')) {
            $query->where('wallet_id', $request->input('wallet_id'));
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->input('category_id'));
        }

        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->input('search') . '%');
        }

        $transactions = $query->latest('transaction_date')->paginate(12)->withQueryString();
        $wallets = $user->wallets;
        $categories = Category::where('user_id', $user->id)->orWhereNull('user_id')->get();

        return view('transactions.index', compact('transactions', 'wallets', 'categories'));
    }

    public function create(): View
    {
        $user = auth()->user();
        $wallets = $user->wallets;
        $categories = Category::where('user_id', $user->id)->orWhereNull('user_id')->get();

        return view('transactions.create', compact('wallets', 'categories'));
    }

    public function store(Request $request): RedirectResponse
    {
        $user = auth()->user();

        $validated = $request->validate([
            'wallet_id' => ['required', 'exists:wallets,id'],
            'category_id' => ['required', 'exists:categories,id'],
            'type' => ['required', 'in:income,expense'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'transaction_date' => ['required', 'date'],
            'title' => ['required', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $validated['user_id'] = $user->id;
        Transaction::create($validated);

        return redirect()->route('transactions.index')->with('status', 'Transaction recorded successfully.');
    }

    public function edit(Transaction $transaction): View
    {
        $this->authorizeAccess($transaction);

        $user = auth()->user();
        $wallets = $user->wallets;
        $categories = Category::where('user_id', $user->id)->orWhereNull('user_id')->get();

        return view('transactions.edit', compact('transaction', 'wallets', 'categories'));
    }

    public function update(Request $request, Transaction $transaction): RedirectResponse
    {
        $this->authorizeAccess($transaction);

        $validated = $request->validate([
            'wallet_id' => ['required', 'exists:wallets,id'],
            'category_id' => ['required', 'exists:categories,id'],
            'type' => ['required', 'in:income,expense'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'transaction_date' => ['required', 'date'],
            'title' => ['required', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $transaction->update($validated);

        return redirect()->route('transactions.index')->with('status', 'Transaction updated.');
    }

    public function destroy(Transaction $transaction): RedirectResponse
    {
        $this->authorizeAccess($transaction);

        $transaction->delete();

        return redirect()->route('transactions.index')->with('status', 'Transaction deleted.');
    }

    private function authorizeAccess(Transaction $transaction): void
    {
        if ($transaction->user_id !== auth()->id() && !auth()->user()->isAdmin()) {
            abort(403);
        }
    }
}
