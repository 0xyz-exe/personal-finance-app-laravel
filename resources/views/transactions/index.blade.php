<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Transaction History') }}
            </h2>
            <a href="{{ route('transactions.create') }}" class="px-4 py-2 bg-emerald-600 text-white rounded-lg text-sm font-semibold hover:bg-emerald-700 transition">
                + New Transaction
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('status'))
                <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-lg text-sm">
                    {{ session('status') }}
                </div>
            @endif

            <!-- Search & Filters -->
            <div class="bg-white p-4 rounded-xl border border-gray-100 shadow-sm">
                <form method="GET" action="{{ route('transactions.index') }}" class="grid grid-cols-1 sm:grid-cols-4 gap-3">
                    <div>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search title..." class="w-full text-sm rounded-lg border-gray-300">
                    </div>
                    <div>
                        <select name="type" class="w-full text-sm rounded-lg border-gray-300">
                            <option value="">All Types (Income / Expense)</option>
                            <option value="expense" {{ request('type') === 'expense' ? 'selected' : '' }}>Expense Only</option>
                            <option value="income" {{ request('type') === 'income' ? 'selected' : '' }}>Income Only</option>
                        </select>
                    </div>
                    <div>
                        <select name="wallet_id" class="w-full text-sm rounded-lg border-gray-300">
                            <option value="">All Wallets</option>
                            @foreach($wallets as $w)
                                <option value="{{ $w->id }}" {{ request('wallet_id') == $w->id ? 'selected' : '' }}>{{ $w->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex gap-2">
                        <select name="category_id" class="w-full text-sm rounded-lg border-gray-300">
                            <option value="">All Categories</option>
                            @foreach($categories as $c)
                                <option value="{{ $c->id }}" {{ request('category_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                            @endforeach
                        </select>
                        <button type="submit" class="px-4 py-2 bg-slate-800 text-white rounded-lg text-sm font-semibold hover:bg-slate-700">Filter</button>
                    </div>
                </form>
            </div>

            <!-- Transactions Table -->
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="text-xs text-gray-500 uppercase bg-gray-50 border-b border-gray-100">
                            <tr>
                                <th class="px-6 py-3">Date</th>
                                <th class="px-6 py-3">Description</th>
                                <th class="px-6 py-3">Account / Wallet</th>
                                <th class="px-6 py-3">Category</th>
                                <th class="px-6 py-3 text-right">Amount</th>
                                <th class="px-6 py-3 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($transactions as $tx)
                                <tr class="hover:bg-gray-50/50 transition">
                                    <td class="px-6 py-4 text-xs text-gray-500 whitespace-nowrap">
                                        {{ $tx->transaction_date->format('M d, Y') }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="font-bold text-gray-900">{{ $tx->title }}</div>
                                        @if($tx->notes)
                                            <div class="text-xs text-gray-400 mt-0.5">{{ $tx->notes }}</div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-xs font-medium text-gray-800">
                                        {{ $tx->wallet->name }}
                                    </td>
                                    <td class="px-6 py-4 text-xs">
                                        <span class="px-2.5 py-0.5 rounded-full font-semibold bg-gray-100 text-gray-700">
                                            {{ $tx->category->name }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-right font-mono font-bold whitespace-nowrap {{ $tx->type === 'income' ? 'text-emerald-600' : 'text-rose-600' }}">
                                        {{ $tx->type === 'income' ? '+' : '-' }}${{ number_format($tx->amount, 2) }}
                                    </td>
                                    <td class="px-6 py-4 text-right space-x-2 whitespace-nowrap">
                                        <a href="{{ route('transactions.edit', $tx) }}" class="text-slate-600 hover:text-slate-900 font-semibold text-xs">Edit</a>
                                        <form method="POST" action="{{ route('transactions.destroy', $tx) }}" class="inline" onsubmit="return confirm('Delete this transaction?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-rose-500 hover:text-rose-700 font-semibold text-xs">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                                        No transactions found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($transactions->hasPages())
                    <div class="px-6 py-4 border-t border-gray-100">
                        {{ $transactions->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
