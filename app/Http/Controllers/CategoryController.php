<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();
        $categories = Category::where(function ($q) use ($user) {
            $q->where('user_id', $user->id)->orWhereNull('user_id');
        })->withCount('transactions')->get();

        return view('categories.index', compact('categories'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'type' => ['required', 'in:income,expense'],
            'color' => ['nullable', 'string', 'max:20'],
            'budget_limit' => ['nullable', 'numeric', 'min:0'],
        ]);

        $validated['user_id'] = auth()->id();
        Category::create($validated);

        return redirect()->route('categories.index')->with('status', 'Category created successfully.');
    }

    public function update(Request $request, Category $category): RedirectResponse
    {
        $this->authorizeAccess($category);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'color' => ['nullable', 'string', 'max:20'],
            'budget_limit' => ['nullable', 'numeric', 'min:0'],
        ]);

        $category->update($validated);

        return redirect()->route('categories.index')->with('status', 'Category / Budget limit updated.');
    }

    public function destroy(Category $category): RedirectResponse
    {
        $this->authorizeAccess($category);

        $category->delete();

        return redirect()->route('categories.index')->with('status', 'Category deleted.');
    }

    private function authorizeAccess(Category $category): void
    {
        if ($category->user_id !== auth()->id() && !auth()->user()->isAdmin()) {
            abort(403);
        }
    }
}
