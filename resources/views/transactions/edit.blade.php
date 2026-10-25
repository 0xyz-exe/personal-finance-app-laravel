<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Transaction') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
                <form method="POST" action="{{ route('transactions.update', $transaction) }}" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 uppercase">Transaction Type *</label>
                            <select name="type" required class="mt-1 w-full text-sm rounded-lg border-gray-300">
                                <option value="expense" {{ old('type', $transaction->type) === 'expense' ? 'selected' : '' }}>Expense (Debit)</option>
                                <option value="income" {{ old('type', $transaction->type) === 'income' ? 'selected' : '' }}>Income (Credit)</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 uppercase">Amount ($) *</label>
                            <input type="number" step="0.01" name="amount" value="{{ old('amount', $transaction->amount) }}" required class="mt-1 w-full text-sm rounded-lg border-gray-300 font-mono">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 uppercase">Description / Payee *</label>
                        <input type="text" name="title" value="{{ old('title', $transaction->title) }}" required class="mt-1 w-full text-sm rounded-lg border-gray-300">
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 uppercase">Wallet / Account *</label>
                            <select name="wallet_id" required class="mt-1 w-full text-sm rounded-lg border-gray-300">
                                @foreach($wallets as $w)
                                    <option value="{{ $w->id }}" {{ old('wallet_id', $transaction->wallet_id) == $w->id ? 'selected' : '' }}>
                                        {{ $w->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 uppercase">Category *</label>
                            <select name="category_id" required class="mt-1 w-full text-sm rounded-lg border-gray-300">
                                @foreach($categories as $c)
                                    <option value="{{ $c->id }}" {{ old('category_id', $transaction->category_id) == $c->id ? 'selected' : '' }}>
                                        [{{ strtoupper($c->type) }}] {{ $c->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 uppercase">Transaction Date *</label>
                        <input type="date" name="transaction_date" value="{{ old('transaction_date', $transaction->transaction_date->format('Y-m-d')) }}" required class="mt-1 w-full text-sm rounded-lg border-gray-300">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 uppercase">Notes</label>
                        <textarea name="notes" rows="2" class="mt-1 w-full text-sm rounded-lg border-gray-300">{{ old('notes', $transaction->notes) }}</textarea>
                    </div>

                    <div class="flex justify-end gap-3 pt-4 border-t">
                        <a href="{{ route('transactions.index') }}" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg text-sm font-semibold hover:bg-gray-200">Cancel</a>
                        <button type="submit" class="px-4 py-2 bg-emerald-600 text-white rounded-lg text-sm font-semibold hover:bg-emerald-700">Update Transaction</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
