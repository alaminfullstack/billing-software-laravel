<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $query = Category::query();

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->has('type') && $request->type != '' && $request->type != 'all') {
            $query->where('type', $request->type);
        }

        $categories = $query->orderBy('name')->paginate(15)->withQueryString();

        // Get statistics for tabs
        $totalCategories = Category::count();
        $activeCategories = Category::where('is_active', true)->count();
        
        // Get category counts by type
        $productCategories = Category::where('type', 'product')->count();
        $expenseCategories = Category::where('type', 'expense')->count();
        $incomeCategories = Category::where('type', 'income')->count();
        $serviceCategories = Category::where('type', 'service')->count();

        // Get most used category
        $mostUsedCategory = Category::withCount(['products', 'services'])
                                   ->orderByDesc('products_count')
                                   ->orderByDesc('services_count')
                                   ->first();

        // Get category tree for hierarchy view
        $categoryTree = Category::with('children.products', 'children.services')
                               ->where('parent_id', null)
                               ->orderBy('name')
                               ->get();

        // Calculate usage count for each category
        $categories->each(function ($category) {
            $category->usage_count = ($category->products->count() ?? 0) + ($category->services->count() ?? 0);
            $category->maxUsage = 1; // Will be updated after collection
        });

        // Get max usage for progress bar calculation
        $maxUsage = $categories->max(function ($category) {
            return ($category->products->count() ?? 0) + ($category->services->count() ?? 0);
        }) ?: 1;

        return view('categories.index', compact(
            'categories',
            'totalCategories',
            'activeCategories',
            'productCategories',
            'expenseCategories',
            'incomeCategories',
            'serviceCategories',
            'mostUsedCategory',
            'categoryTree',
            'maxUsage'
        ));
    }

    public function show(Category $category)
    {
        $category->load('products', 'services');
        return view('categories.show', compact('category'));
    }

    public function create()
    {
        $parentCategories = Category::where('parent_id', null)
                                   ->orderBy('name')
                                   ->get();
        return view('categories.create', compact('parentCategories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories',
            'description' => 'nullable|string',
            'type' => 'required|in:product,expense,income,service',
            'parent_id' => 'nullable|exists:categories,id',
            'color' => 'nullable|string',
            'icon' => 'nullable|string',
            'is_active' => 'boolean',
            'is_taxable' => 'boolean',
            'default_tax_rate' => 'nullable|numeric|min:0|max:100',
            'account_code' => 'nullable|string',
            'is_default' => 'boolean',
            'show_in_menu' => 'boolean',
            'allow_subcategories' => 'boolean',
        ]);

        Category::create($validated);

        return redirect()->route('categories.index')
                        ->with('success', 'Category created successfully.');
    }

    public function edit(Category $category)
    {
        $parentCategories = Category::where('parent_id', null)
                                   ->where('id', '!=', $category->id)
                                   ->orderBy('name')
                                   ->get();
        return view('categories.edit', compact('category', 'parentCategories'));
    }

    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:categories,name,' . $category->id],
            'description' => 'nullable|string',
            'type' => 'required|in:product,expense,income,service',
            'parent_id' => 'nullable|exists:categories,id',
            'color' => 'nullable|string',
            'icon' => 'nullable|string',
            'is_active' => 'boolean',
            'is_taxable' => 'boolean',
            'default_tax_rate' => 'nullable|numeric|min:0|max:100',
            'account_code' => 'nullable|string',
            'is_default' => 'boolean',
            'show_in_menu' => 'boolean',
            'allow_subcategories' => 'boolean',
        ]);

        $category->update($validated);

        return redirect()->route('categories.index')
                        ->with('success', 'Category updated successfully.');
    }

    public function destroy(Category $category)
    {
        if ($category->products()->count() > 0 || $category->services()->count() > 0) {
            return back()->with('error', 'Cannot delete category with existing products or services.');
        }

        $category->delete();

        return redirect()->route('categories.index')
                        ->with('success', 'Category deleted successfully.');
    }
}
