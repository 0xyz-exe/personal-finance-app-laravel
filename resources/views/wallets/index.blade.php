<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Wallets & Accounts') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('status'))
                <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-lg text-sm">
                    {{ session('status') }}
                </div>
            @endif

            <!-- Create Wallet Card -->
            <div class="bg-white p-6 rounded-xl border border-gray-100 shadow-sm" x-data="{ open: false }">
                <div class="flex justify-between items-center cursor-pointer" @click="open = !open">
                    <div class="flex items-center gap-2">
                        <span class="p-2 rounded-lg bg-emerald-50 text-emerald-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                        </span>
                        <h3 class="font-bold text-gray-900">Add New Wallet / Bank Account</h3>
                    </div>
                    <button type="button" class="text-sm font-semibold text-emerald-600 hover:text-emerald-800" x-text="open ? 'Close' : '+ Add Wallet'"></button>
                </div>

                <form x-show="open" method="POST" action="{{ route('wallets.store') }}" class="mt-4 pt-4 border-t space-y-4">
                    @csrf
                    <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 uppercase">Wallet Name *</label>
                            <input type="text" name="name" required placeholder="e.g. Chase Checking" class="mt-1 w-full text-sm rounded-lg border-gray-300">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 uppercase">Account Type *</label>
                            <select name="type" required class="mt-1 w-full text-sm rounded-lg border-gray-300">
                                <option value="bank">Bank Account</option>
                                <option value="cash">Cash in Hand</option>
                                <option value="credit_card">Credit Card</option>
                                <option value="savings">Savings Account</option>
                                <option value="e_wallet">E-Wallet / PayPal</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 uppercase">Starting Balance ($) *</label>
                            <input type="number" step="0.01" name="initial_balance" value="0.00" required class="mt-1 w-full text-sm rounded-lg border-gray-300">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 uppercase">Currency *</label>
                            <input type="text" name="currency" value="USD" required class="mt-1 w-full text-sm rounded-lg border-gray-300">
                        </div>
                    </div>
                    <div class="flex justify-end">
                        <button type="submit" class="px-4 py-2 bg-emerald-600 text-white rounded-lg text-sm font-semibold hover:bg-emerald-700 transition">Save Wallet</button>
                    </div>
                </form>
            </div>

            <!-- Wallets Grid -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @foreach($wallets as $wallet)
                    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 flex flex-col justify-between" x-data="{ openEdit: false }">
                        <div>
                            <div class="flex justify-between items-start">
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold uppercase bg-gray-100 text-gray-700">
                                    {{ str_replace('_', ' ', $wallet->type) }}
                                </span>
                                <div class="flex gap-2">
                                    <button @click="openEdit = !openEdit" class="text-xs font-semibold text-slate-600 hover:text-slate-900">Edit</button>
                                    <form method="POST" action="{{ route('wallets.destroy', $wallet) }}" class="inline" onsubmit="return confirm('Delete this wallet? All associated transactions will be removed.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-xs font-semibold text-rose-500 hover:text-rose-700">Delete</button>
                                    </form>
                                </div>
                            </div>

                            <div class="mt-4">
                                <h3 class="text-lg font-bold text-gray-900">{{ $wallet->name }}</h3>
                                <p class="text-2xl font-black text-gray-900 font-mono mt-1">${{ number_format($wallet->balance, 2) }}</p>
                                <p class="text-xs text-gray-400 mt-1">Starting balance: ${{ number_format($wallet->initial_balance, 2) }}</p>
                            </div>

                            <!-- Inline Edit Form -->
                            <form x-show="openEdit" method="POST" action="{{ route('wallets.update', $wallet) }}" class="mt-4 pt-4 border-t space-y-3">
                                @csrf
                                @method('PUT')
                                <div>
                                    <label class="block text-[11px] font-semibold text-gray-600 uppercase">Wallet Name</label>
                                    <input type="text" name="name" value="{{ $wallet->name }}" required class="w-full text-xs rounded border-gray-300">
                                </div>
                                <div class="grid grid-cols-2 gap-2">
                                    <div>
                                        <label class="block text-[11px] font-semibold text-gray-600 uppercase">Type</label>
                                        <select name="type" class="w-full text-xs rounded border-gray-300">
                                            @foreach(['bank', 'cash', 'credit_card', 'savings', 'e_wallet'] as $t)
                                                <option value="{{ $t }}" {{ $wallet->type === $t ? 'selected' : '' }}>{{ $t }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-[11px] font-semibold text-gray-600 uppercase">Initial Balance</label>
                                        <input type="number" step="0.01" name="initial_balance" value="{{ $wallet->initial_balance }}" required class="w-full text-xs rounded border-gray-300">
                                    </div>
                                </div>
                                <input type="hidden" name="currency" value="{{ $wallet->currency }}">
                                <div class="flex justify-end gap-2">
                                    <button type="button" @click="openEdit = false" class="px-2 py-1 bg-gray-100 rounded text-xs">Cancel</button>
                                    <button type="submit" class="px-3 py-1 bg-emerald-600 text-white rounded text-xs font-semibold">Update</button>
                                </div>
                            </form>
                        </div>

                        <div class="mt-6 pt-4 border-t border-gray-100 flex justify-between text-xs text-gray-500">
                            <span>Transactions logged:</span>
                            <span class="font-bold text-gray-900">{{ $wallet->transactions_count }}</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</x-app-layout>
