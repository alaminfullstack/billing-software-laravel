<?php

namespace App\Http\Controllers;

use App\Models\InventoryTransaction;
use App\Models\Product;
use Illuminate\Http\Request;

class InventoryTransactionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $transactions = InventoryTransaction::with(['product', 'user'])
            ->when($request->product_id, function ($query, $productId) {
                return $query->where('product_id', $productId);
            })
            ->when($request->type, function ($query, $type) {
                return $query->where('type', $type);
            })
            ->when($request->date_from, function ($query, $dateFrom) {
                return $query->whereDate('created_at', '>=', $dateFrom);
            })
            ->when($request->date_to, function ($query, $dateTo) {
                return $query->whereDate('created_at', '<=', $dateTo);
            })
            ->when($request->search, function ($query, $search) {
                return $query->where(function ($q) use ($search) {
                    $q->whereHas('product', function ($q) use ($search) {
                            $q->where('name', 'like', "%{$search}%")
                              ->orWhere('sku', 'like', "%{$search}%");
                        })
                      ->orWhere('notes', 'like', "%{$search}%");
                });
            })
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $products = Product::active()->orderBy('name')->get();
        
        $stats = [
            'total' => InventoryTransaction::count(),
            'purchases' => InventoryTransaction::purchases()->count(),
            'sales' => InventoryTransaction::sales()->count(),
            'adjustments' => InventoryTransaction::adjustments()->count(),
            'totalQuantity' => InventoryTransaction::sum('quantity'),
            'totalCost' => InventoryTransaction::sum(\DB::raw('quantity * unit_cost')),
        ];

        return view('inventory_transactions.index', compact('transactions', 'stats', 'products'));
    }

    public function show(InventoryTransaction $transaction)
    {
        $transaction->load(['product', 'user', 'reference']);

        return view('inventory_transactions.show', compact('transaction'));
    }

    public function create()
    {
        $products = Product::active()->orderBy('name')->get();
        
        return view('inventory_transactions.create', compact('products'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'type' => 'required|in:purchase,sale,adjustment,return,transfer',
            'quantity' => 'required|integer',
            'unit_cost' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $product = Product::findOrFail($validated['product_id']);
        
        $previousStock = $product->stock_quantity;
        
        // Calculate new stock based on transaction type
        switch ($validated['type']) {
            case 'purchase':
            case 'return':
                $newStock = $previousStock + abs($validated['quantity']);
                break;
            case 'sale':
                $newStock = $previousStock - abs($validated['quantity']);
                break;
            case 'adjustment':
            case 'transfer':
                $newStock = $previousStock + $validated['quantity'];
                break;
            default:
                $newStock = $previousStock;
        }

        if ($newStock < 0) {
            return redirect()->back()
                ->withErrors(['quantity' => 'Transaction would result in negative stock.'])
                ->withInput();
        }

        $transaction = InventoryTransaction::create([
            'product_id' => $validated['product_id'],
            'user_id' => auth()->id(),
            'type' => $validated['type'],
            'quantity' => abs($validated['quantity']),
            'previous_stock' => $previousStock,
            'new_stock' => $newStock,
            'unit_cost' => $validated['unit_cost'],
            'notes' => $validated['notes'],
        ]);

        // Update product stock
        $product->update(['stock_quantity' => $newStock]);

        return redirect()->route('inventory-transactions.show', $transaction)
            ->with('success', 'Inventory transaction recorded successfully.');
    }

    public function getStockHistory($productId)
    {
        $transactions = InventoryTransaction::where('product_id', $productId)
            ->with(['user'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'transactions' => $transactions,
            'current_stock' => Product::find($productId)->stock_quantity
        ]);
    }

    public function getLowStockProducts()
    {
        $lowStockProducts = Product::active()
            ->whereColumn('stock_quantity', '<=', 'low_stock_alert')
            ->with(['category'])
            ->get();

        return response()->json($lowStockProducts);
    }
}
