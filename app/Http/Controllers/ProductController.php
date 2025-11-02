<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Service;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    public function products(Request $request)
    {
        $query = Product::with('category');

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->has('category_id') && $request->category_id != '') {
            $query->where('category_id', $request->category_id);
        }

        $products = $query->orderBy('created_at', 'desc')
                         ->paginate(15)
                         ->withQueryString();

        $categories = Category::where('type', 'product')
                             ->where('is_active', true)
                             ->orderBy('name')
                             ->get();

        return view('products.index', compact('products', 'categories'));
    }

    public function services(Request $request)
    {
        $query = Service::with('category');

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->has('category_id') && $request->category_id != '') {
            $query->where('category_id', $request->category_id);
        }

        $services = $query->orderBy('created_at', 'desc')
                         ->paginate(15)
                         ->withQueryString();

        $categories = Category::where('type', 'service')
                             ->where('is_active', true)
                             ->orderBy('name')
                             ->get();

        return view('services.index', compact('services', 'categories'));
    }

    public function createProduct()
    {
        $categories = Category::where('type', 'product')
                             ->where('is_active', true)
                             ->orderBy('name')
                             ->get();
        return view('products.create', compact('categories'));
    }

    public function createService()
    {
        $categories = Category::where('type', 'service')
                             ->where('is_active', true)
                             ->orderBy('name')
                             ->get();
        return view('services.create', compact('categories'));
    }

    public function storeProduct(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'sku' => 'required|string|unique:products,sku|max:50',
            'unit_price' => 'required|numeric|min:0',
            'cost_price' => 'nullable|numeric|min:0',
            'tax_rate' => 'nullable|numeric|min:0|max:100',
            'track_inventory' => 'boolean',
            'stock_quantity' => 'required_if:track_inventory,1|integer|min:0',
            'reorder_level' => 'required_if:track_inventory,1|integer|min:0',
            'low_stock_alert' => 'required_if:track_inventory,1|integer|min:0',
        ]);

        if (!$request->track_inventory) {
            $validated['track_inventory'] = false;
            $validated['stock_quantity'] = 0;
            $validated['reorder_level'] = 0;
            $validated['low_stock_alert'] = 0;
        }

        Product::create($validated);

        return redirect()->route('products.index')
                        ->with('success', 'Product created successfully.');
    }

    public function storeService(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'hourly_rate' => 'required|numeric|min:0',
        ]);

        Service::create($validated);

        return redirect()->route('services.index')
                        ->with('success', 'Service created successfully.');
    }

    public function showProduct(Product $product)
    {
        $product->load('category', 'invoiceItems.invoice');
        return view('products.show', compact('product'));
    }

    public function showService(Service $service)
    {
        $service->load('category', 'invoiceItems.invoice');
        return view('services.show', compact('service'));
    }

    public function editProduct(Product $product)
    {
        $categories = Category::products()->orderBy('name')->get();
        return view('products.edit', compact('product', 'categories'));
    }

    public function editService(Service $service)
    {
        $categories = Category::services()->orderBy('name')->get();
        return view('services.edit', compact('service', 'categories'));
    }

    public function updateProduct(Request $request, Product $product)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'sku' => ['required', 'string', 'max:50', 'unique:products,sku,' . $product->id],
            'unit_price' => 'required|numeric|min:0',
            'cost_price' => 'nullable|numeric|min:0',
            'tax_rate' => 'nullable|numeric|min:0|max:100',
            'track_inventory' => 'boolean',
            'stock_quantity' => 'required_if:track_inventory,1|integer|min:0',
            'reorder_level' => 'required_if:track_inventory,1|integer|min:0',
            'low_stock_alert' => 'required_if:track_inventory,1|integer|min:0',
        ]);

        if (!$request->track_inventory) {
            $validated['track_inventory'] = false;
            $validated['stock_quantity'] = 0;
            $validated['reorder_level'] = 0;
            $validated['low_stock_alert'] = 0;
        }

        $product->update($validated);

        return redirect()->route('products.index')
                        ->with('success', 'Product updated successfully.');
    }

    public function updateService(Request $request, Service $service)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'hourly_rate' => 'required|numeric|min:0',
        ]);

        $service->update($validated);

        return redirect()->route('services.index')
                        ->with('success', 'Service updated successfully.');
    }

    public function destroyProduct(Product $product)
    {
        $product->delete();

        return redirect()->route('products.index')
                        ->with('success', 'Product deleted successfully.');
    }

    public function destroyService(Service $service)
    {
        $service->delete();

        return redirect()->route('services.index')
                        ->with('success', 'Service deleted successfully.');
    }
}
