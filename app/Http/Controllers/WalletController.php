<?php

namespace App\Http\Controllers;

use App\Models\Wallet;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WalletController extends Controller
{
    public function index(): View
    {
        $wallets = auth()->user()->wallets()->withCount('transactions')->get();

        return view('wallets.index', compact('wallets'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'type' => ['required', 'in:bank,cash,credit_card,savings,e_wallet'],
            'initial_balance' => ['required', 'numeric', 'min:0'],
            'currency' => ['required', 'string', 'max:10'],
            'color' => ['nullable', 'string', 'max:20'],
        ]);

        auth()->user()->wallets()->create($validated);

        return redirect()->route('wallets.index')->with('status', 'Wallet / Account created.');
    }

    public function update(Request $request, Wallet $wallet): RedirectResponse
    {
        $this->authorizeAccess($wallet);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'type' => ['required', 'in:bank,cash,credit_card,savings,e_wallet'],
            'initial_balance' => ['required', 'numeric'],
            'currency' => ['required', 'string', 'max:10'],
            'color' => ['nullable', 'string', 'max:20'],
        ]);

        $wallet->update($validated);

        return redirect()->route('wallets.index')->with('status', 'Wallet updated.');
    }

    public function destroy(Wallet $wallet): RedirectResponse
    {
        $this->authorizeAccess($wallet);

        $wallet->delete();

        return redirect()->route('wallets.index')->with('status', 'Wallet deleted.');
    }

    private function authorizeAccess(Wallet $wallet): void
    {
        if ($wallet->user_id !== auth()->id() && !auth()->user()->isAdmin()) {
            abort(403);
        }
    }
}
