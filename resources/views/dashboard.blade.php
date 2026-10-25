<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Financial Overview') }}
            </h2>
            <div class="flex gap-2">
                <a href="{{ route('transactions.create') }}" class="px-4 py-2 bg-emerald-600 text-white rounded-lg text-sm font-semibold hover:bg-emerald-700 transition">
                    + Log Transaction
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('status'))
                <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-lg text-sm">
                    {{ session('status') }}
                </div>
            @endif

            <!-- Balance & Cashflow Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
                <div class="bg-white p-5 rounded-xl border border-gray-100 shadow-sm">
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Total Net Worth</p>
                    <p class="text-3xl font-extrabold text-gray-900 mt-2 font-mono">${{ number_format($totalBalance, 2) }}</p>
                    <p class="text-xs text-gray-400 font-medium mt-1">Across {{ $wallets->count() }} connected wallets</p>
                </div>
                <div class="bg-white p-5 rounded-xl border border-gray-100 shadow-sm">
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">This Month's Income</p>
                    <p class="text-3xl font-extrabold text-emerald-600 mt-2 font-mono">+${{ number_format($thisMonthIncome, 2) }}</p>
                    <p class="text-xs text-emerald-600 font-medium mt-1">{{ now()->format('F Y') }}</p>
                </div>
                <div class="bg-white p-5 rounded-xl border border-gray-100 shadow-sm">
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">This Month's Expenses</p>
                    <p class="text-3xl font-extrabold text-rose-600 mt-2 font-mono">-${{ number_format($thisMonthExpense, 2) }}</p>
                    <p class="text-xs text-rose-500 font-medium mt-1">{{ now()->format('F Y') }}</p>
                </div>
                <div class="bg-white p-5 rounded-xl border border-gray-100 shadow-sm">
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Net Cashflow</p>
                    <p class="text-3xl font-extrabold font-mono mt-2 {{ $netCashflow >= 0 ? 'text-emerald-700' : 'text-rose-700' }}">
                        {{ $netCashflow >= 0 ? '+' : '' }}${{ number_format($netCashflow, 2) }}
                    </p>
                    <p class="text-xs text-gray-400 font-medium mt-1">Income minus expenses</p>
                </div>
            </div>

            <!-- Connected Wallets Overview -->
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6 space-y-4">
                <div class="flex justify-between items-center">
                    <h3 class="text-base font-bold text-gray-900">Wallets & Accounts</h3>
                    <a href="{{ route('wallets.index') }}" class="text-xs font-semibold text-emerald-600 hover:text-emerald-800">
                        Manage Wallets &rarr;
                    </a>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    @forelse($wallets as $wallet)
                        <div class="p-4 rounded-xl border border-gray-100 bg-gray-50/50 flex flex-col justify-between">
                            <div class="flex justify-between items-start">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-white border border-gray-200 text-gray-600">
                                    {{ str_replace('_', ' ', $wallet->type) }}
                                </span>
                                <div class="w-3 h-3 rounded-full" style="background-color: {{ $wallet->color }};"></div>
                            </div>
                            <div class="mt-3">
                                <p class="text-xs text-gray-500">{{ $wallet->name }}</p>
                                <p class="text-xl font-extrabold text-gray-900 font-mono mt-0.5">${{ number_format($wallet->balance, 2) }}</p>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-4 py-4 text-center text-xs text-gray-400">
                            No wallets registered yet.
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Split: Recent Transactions & Budget Progress -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Recent Transactions -->
                <div class="lg:col-span-2 bg-white rounded-xl border border-gray-100 shadow-sm p-6 space-y-4">
                    <div class="flex justify-between items-center">
                        <h3 class="text-base font-bold text-gray-900">Recent Transactions</h3>
                        <a href="{{ route('transactions.index') }}" class="text-xs font-semibold text-emerald-600 hover:text-emerald-800">
                            View All &rarr;
                        </a>
                    </div>

                    @if($recentTransactions->isEmpty())
                        <p class="text-xs text-gray-400 italic py-6 text-center">No transactions recorded yet.</p>
                    @else
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm text-left">
                                <thead class="text-xs text-gray-400 uppercase bg-gray-50 border-b">
                                    <tr>
                                        <th class="px-4 py-3">Date</th>
                                        <th class="px-4 py-3">Description</th>
                                        <th class="px-4 py-3">Wallet & Category</th>
                                        <th class="px-4 py-3 text-right">Amount</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    @foreach($recentTransactions as $tx)
                                        <tr>
                                            <td class="px-4 py-3 text-xs text-gray-500 whitespace-nowrap">
                                                {{ $tx->transaction_date->format('M d') }}
                                            </td>
                                            <td class="px-4 py-3 font-semibold text-gray-900">
                                                {{ $tx->title }}
                                            </td>
                                            <td class="px-4 py-3 text-xs">
                                                <span class="font-medium text-gray-700">{{ $tx->wallet->name }}</span>
                                                <span class="text-gray-400">&bull; {{ $tx->category->name }}</span>
                                            </td>
                                            <td class="px-4 py-3 text-right font-mono font-bold whitespace-nowrap {{ $tx->type === 'income' ? 'text-emerald-600' : 'text-rose-600' }}">
                                                {{ $tx->type === 'income' ? '+' : '-' }}${{ number_format($tx->amount, 2) }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>

                <!-- Budget Limits & Alerts -->
                <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6 space-y-4">
                    <div class="flex justify-between items-center">
                        <h3 class="text-base font-bold text-gray-900">Budget Watch</h3>
                        <a href="{{ route('categories.index') }}" class="text-xs font-semibold text-emerald-600 hover:text-emerald-800">
                            Limits &rarr;
                        </a>
                    </div>

                    @if($budgetAlerts->isEmpty())
                        <p class="text-xs text-gray-400 italic py-6 text-center">No active monthly budgets configured.</p>
                    @else
                        <div class="space-y-4">
                            @foreach($budgetAlerts as $category)
                                @php
                                    $pct = $category->budget_usage_percent;
                                    $isOver = $category->monthly_spent > $category->budget_limit;
                                @endphp
                                <div class="space-y-1.5 text-xs">
                                    <div class="flex justify-between font-semibold">
                                        <span class="text-gray-900">{{ $category->name }}</span>
                                        <span class="{{ $isOver ? 'text-rose-600 font-bold' : 'text-gray-600' }}">
                                            ${{ number_format($category->monthly_spent, 2) }} / ${{ number_format($category->budget_limit, 2) }}
                                        </span>
                                    </div>
                                    <!-- Progress Bar -->
                                    <div class="w-full bg-gray-100 rounded-full h-2 overflow-hidden">
                                        <div class="h-2 rounded-full transition-all {{ $isOver ? 'bg-rose-500' : ($pct > 80 ? 'bg-amber-500' : 'bg-emerald-500') }}" style="width: {{ $pct }}%;"></div>
                                    </div>
                                    <div class="flex justify-between text-[11px] text-gray-400">
                                        <span>{{ $pct }}% utilized</span>
                                        @if($isOver)
                                            <span class="text-rose-600 font-bold">Over limit!</span>
                                        @else
                                            <span>${{ number_format($category->budget_limit - $category->monthly_spent, 2) }} left</span>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
