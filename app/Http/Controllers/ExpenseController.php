<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\ExpenseCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;

class ExpenseController extends Controller
{
    public function index(Request $request)
    {
        $query = Expense::where('created_by', Auth::id())->with(['category', 'supplier']);

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                  ->orWhere('vendor', 'like', "%{$search}%")
                  ->orWhereHas('category', function ($categoryQuery) use ($search) {
                      $categoryQuery->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('supplier', function ($supplierQuery) use ($search) {
                      $supplierQuery->where('name', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->has('category_id') && $request->category_id != '') {
            $query->where('category_id', $request->category_id);
        }

        if ($request->has('supplier_id') && $request->supplier_id != '') {
            $query->where('supplier_id', $request->supplier_id);
        }

        if ($request->has('payment_method') && $request->payment_method != '') {
            $query->where('payment_method', $request->payment_method);
        }

        if ($request->has('date_from')) {
            $query->where('expense_date', '>=', $request->date_from);
        }

        if ($request->has('date_to')) {
            $query->where('expense_date', '<=', $request->date_to);
        }

        $expenses = $query->orderBy('expense_date', 'desc')
                         ->paginate(15)
                         ->withQueryString();

        $categories = ExpenseCategory::orderBy('name')->get();
        $suppliers = \App\Models\Supplier::orderBy('name')->get();

        return view('expenses.index', compact('expenses', 'categories', 'suppliers'));
    }

    public function show(Expense $expense)
    {
        Gate::authorize('view', $expense);
        
        $expense->load('category', 'creator', 'supplier');
        
        return view('expenses.show', compact('expense'));
    }

    public function create()
    {
        Gate::authorize('create', Expense::class);
        
        $categories = ExpenseCategory::orderBy('name')->get();
        $suppliers = \App\Models\Supplier::orderBy('name')->get();

        return view('expenses.create', compact('categories', 'suppliers'));
    }

    public function store(Request $request)
    {
        Gate::authorize('create', Expense::class);

        $validated = $request->validate([
            'category_id' => 'required|exists:expense_categories,id',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'amount' => 'required|numeric|min:0.01',
            'expense_date' => 'required|date',
            'description' => 'required|string',
            'receipt_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png,gif|max:10240',
            'payment_method' => 'required|in:cash,bank_transfer,credit_card,check,online,other',
            'vendor' => 'nullable|string|max:255',
        ]);

        $validated['created_by'] = Auth::id();
        $validated['expense_category_id'] = $request->category_id;
        $validated['receipt_file'] = null;

        if ($request->hasFile('receipt_file')) {
            $validated['receipt_file'] = $request->file('receipt_file')->store('receipts', 'public');
        }

        Expense::create($validated);

        return redirect()->route('expenses.index')
                        ->with('success', 'Expense recorded successfully.');
    }

    public function edit(Expense $expense)
    {
        Gate::authorize('update', $expense);
        
        $categories = ExpenseCategory::orderBy('name')->get();
        $suppliers = \App\Models\Supplier::orderBy('name')->get();

        return view('expenses.edit', compact('expense', 'categories', 'suppliers'));
    }

    public function update(Request $request, Expense $expense)
    {
        Gate::authorize('update', $expense);

        $validated = $request->validate([
            'category_id' => 'required|exists:expense_categories,id',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'amount' => 'required|numeric|min:0.01',
            'expense_date' => 'required|date',
            'description' => 'required|string',
            'receipt_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png,gif|max:10240',
            'payment_method' => 'required|in:cash,bank_transfer,credit_card,check,online,other',
            'vendor' => 'nullable|string|max:255',
        ]);

        if ($request->hasFile('receipt_file')) {
            // Delete old receipt if exists
            if ($expense->receipt_file) {
                Storage::disk('public')->delete($expense->receipt_file);
            }
            $validated['receipt_file'] = $request->file('receipt_file')->store('receipts', 'public');
        } else {
            $validated['receipt_file'] = $expense->receipt_file;
        }

        $expense->update($validated);

        return redirect()->route('expenses.index')
                        ->with('success', 'Expense updated successfully.');
    }

    public function destroy(Expense $expense)
    {
        Gate::authorize('delete', $expense);

        // Delete receipt file if exists
        if ($expense->receipt_file) {
            Storage::disk('public')->delete($expense->receipt_file);
        }

        $expense->delete();

        return redirect()->route('expenses.index')
                        ->with('success', 'Expense deleted successfully.');
    }

    public function downloadReceipt(Expense $expense)
    {
        Gate::authorize('view', $expense);

        if (!$expense->receipt_file || !Storage::disk('public')->exists($expense->receipt_file)) {
            return back()->with('error', 'Receipt file not found.');
        }

        return Storage::disk('public')->download($expense->receipt_file);
    }
}
