<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller
{

    public function index(Request $request)
    {
        $suppliers = Supplier::with(['expenses'])
            ->when($request->search, function ($query, $search) {
                return $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhere('phone', 'like', "%{$search}%")
                      ->orWhere('tax_number', 'like', "%{$search}%");
                });
            })
            ->when($request->status, function ($query, $status) {
                return $query->where('status', $status);
            })
            ->orderBy('name')
            ->paginate(15);

        $stats = [
            'total' => Supplier::count(),
            'active' => Supplier::active()->count(),
            'business' => Supplier::business()->count(),
            'totalExpenses' => Supplier::withCount('expenses')->get()->sum('expenses_count'),
        ];

        return view('suppliers.index', compact('suppliers', 'stats'));
    }

    public function create()
    {
        return view('suppliers.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:suppliers',
            'email' => 'required|email|unique:suppliers',
            'phone' => 'nullable|string|max:50',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'zip_code' => 'nullable|string|max:20',
            'tax_number' => 'nullable|string|max:100',
            'website' => 'nullable|url',
            'notes' => 'nullable|string',
            'status' => 'required|in:active,inactive,blocked',
        ]);

        Supplier::create($validated);

        return redirect()->route('suppliers.index')
            ->with('success', 'Supplier created successfully.');
    }

    public function show(Supplier $supplier)
    {
        $supplier->load(['expenses.category']);
        
        $expenseStats = [
            'total' => $supplier->expenses->count(),
            'totalAmount' => $supplier->expenses->sum('amount'),
            'pending' => $supplier->expenses->where('status', 'pending')->count(),
            'approved' => $supplier->expenses->where('status', 'approved')->count(),
            'paid' => $supplier->expenses->where('status', 'paid')->count(),
        ];

        return view('suppliers.show', compact('supplier', 'expenseStats'));
    }

    public function edit(Supplier $supplier)
    {
        return view('suppliers.edit', compact('supplier'));
    }

    public function update(Request $request, Supplier $supplier)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:suppliers,name,' . $supplier->id],
            'email' => ['required', 'email', 'unique:suppliers,email,' . $supplier->id],
            'phone' => 'nullable|string|max:50',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'zip_code' => 'nullable|string|max:20',
            'tax_number' => 'nullable|string|max:100',
            'website' => 'nullable|url',
            'notes' => 'nullable|string',
            'status' => 'required|in:active,inactive,blocked',
        ]);

        $supplier->update($validated);

        return redirect()->route('suppliers.index')
            ->with('success', 'Supplier updated successfully.');
    }

    public function destroy(Supplier $supplier)
    {
        if ($supplier->expenses()->count() > 0) {
            return redirect()->route('suppliers.index')
                ->with('error', 'Cannot delete supplier that has associated expenses.');
        }

        $supplier->delete();

        return redirect()->route('suppliers.index')
            ->with('success', 'Supplier deleted successfully.');
    }

    public function toggle(Supplier $supplier)
    {
        $newStatus = $supplier->status === 'active' ? 'inactive' : 'active';
        $supplier->update(['status' => $newStatus]);

        $status = $newStatus === 'active' ? 'activated' : 'deactivated';
        
        return redirect()->route('suppliers.index')
            ->with('success', "Supplier {$status} successfully.");
    }
}
