<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Categories & Monthly Budgets') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('status'))
                <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-lg text-sm">
                    {{ session('status') }}
                </div>
            @endif

            <!-- Create Category Card -->
            <div class="bg-white p-6 rounded-xl border border-gray-100 shadow-sm" x-data="{ open: false }">
                <div class="flex justify-between items-center cursor-pointer" @click="open = !open">
                    <div class="flex items-center gap-2">
                        <span class="p-2 rounded-lg bg-emerald-50 text-emerald-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                        </span>
                        <h3 class="font-bold text-gray-900">Add New Category / Budget Limit</h3>
                    </div>
                    <button type="button" class="text-sm font-semibold text-emerald-600 hover:text-emerald-800" x-text="open ? 'Close' : '+ Add Category'"></button>
                </div>

                <form x-show="open" method="POST" action="{{ route('categories.store') }}" class="mt-4 pt-4 border-t space-y-4">
                    @csrf
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 uppercase">Category Name *</label>
                            <input type="text" name="name" required placeholder="e.g. Groceries, Freelance" class="mt-1 w-full text-sm rounded-lg border-gray-300">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 uppercase">Type *</label>
                            <select name="type" required class="mt-1 w-full text-sm rounded-lg border-gray-300">
                                <option value="expense">Expense (Spend)</option>
                                <option value="income">Income (Revenue)</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 uppercase">Monthly Budget Limit ($) (Optional)</label>
                            <input type="number" step="0.01" name="budget_limit" placeholder="e.g. 500.00" class="mt-1 w-full text-sm rounded-lg border-gray-300">
                        </div>
                    </div>
                    <div class="flex justify-end">
                        <button type="submit" class="px-4 py-2 bg-emerald-600 text-white rounded-lg text-sm font-semibold hover:bg-emerald-700 transition">Save Category</button>
                    </div>
                </form>
            </div>

            <!-- Categories Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Expense Categories & Budget Progress -->
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 space-y-4">
                    <h3 class="font-bold text-gray-900 text-base">Expense Categories & Monthly Limits</h3>
                    <div class="space-y-4">
                        @foreach($categories->where('type', 'expense') as $cat)
                            @php
                                $spent = $cat->monthly_spent;
                                $limit = $cat->budget_limit;
                                $pct = $cat->budget_usage_percent;
                            @endphp
                            <div class="p-3.5 rounded-xl bg-gray-50 border border-gray-100 space-y-2">
                                <div class="flex justify-between items-center text-sm">
                                    <span class="font-bold text-gray-900">{{ $cat->name }}</span>
                                    <div class="flex items-center gap-2">
                                        <span class="text-xs font-semibold {{ $limit && $spent > $limit ? 'text-rose-600' : 'text-gray-600' }}">
                                            ${{ number_format($spent, 2) }} {{ $limit ? '/ $' . number_format($limit, 2) : '' }}
                                        </span>
                                        @if($cat->user_id)
                                            <form method="POST" action="{{ route('categories.destroy', $cat) }}" class="inline" onsubmit="return confirm('Delete category?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-gray-400 hover:text-rose-600 text-xs">&times;</button>
                                            </form>
                                        @endif
                                    </div>
                                </div>

                                @if($limit)
                                    <div class="w-full bg-gray-200 rounded-full h-1.5 overflow-hidden">
                                        <div class="h-1.5 rounded-full {{ $spent > $limit ? 'bg-rose-500' : ($pct > 80 ? 'bg-amber-500' : 'bg-emerald-500') }}" style="width: {{ $pct }}%;"></div>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Income Categories -->
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 space-y-4">
                    <h3 class="font-bold text-gray-900 text-base">Income Sources</h3>
                    <div class="space-y-2">
                        @foreach($categories->where('type', 'income') as $cat)
                            <div class="p-3 rounded-xl bg-emerald-50/50 border border-emerald-100 flex justify-between items-center text-sm">
                                <span class="font-bold text-emerald-900">{{ $cat->name }}</span>
                                <div class="flex items-center gap-2">
                                    <span class="text-xs text-emerald-600 font-medium">{{ $cat->transactions_count }} records</span>
                                    @if($cat->user_id)
                                        <form method="POST" action="{{ route('categories.destroy', $cat) }}" class="inline" onsubmit="return confirm('Delete category?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-gray-400 hover:text-rose-600 text-xs">&times;</button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
