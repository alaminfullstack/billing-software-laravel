<?php

namespace App\Http\Controllers;

use App\Models\Tax;
use Illuminate\Http\Request;

class TaxController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $taxes = Tax::when($request->search, function ($query, $search) {
                return $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('code', 'like', "%{$search}%");
                });
            })
            ->when($request->type, function ($query, $type) {
                return $query->where('type', $type);
            })
            ->orderBy('name')
            ->paginate(15);

        $stats = [
            'total' => Tax::count(),
            'active' => Tax::active()->count(),
            'percentage' => Tax::percentage()->count(),
            'fixed' => Tax::fixed()->count(),
        ];

        return view('taxes.index', compact('taxes', 'stats'));
    }

    public function create()
    {
        return view('taxes.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:taxes',
            'code' => 'required|string|max:50|unique:taxes',
            'rate' => 'required|numeric|min:0',
            'type' => 'required|in:percentage,fixed',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        Tax::create($validated);

        return redirect()->route('taxes.index')
            ->with('success', 'Tax rate created successfully.');
    }

    public function show(Tax $tax)
    {
        $tax->load(['products', 'services']);
        
        return view('taxes.show', compact('tax'));
    }

    public function edit(Tax $tax)
    {
        return view('taxes.edit', compact('tax'));
    }

    public function update(Request $request, Tax $tax)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:taxes,name,' . $tax->id],
            'code' => ['required', 'string', 'max:50', 'unique:taxes,code,' . $tax->id],
            'rate' => 'required|numeric|min:0',
            'type' => 'required|in:percentage,fixed',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $tax->update($validated);

        return redirect()->route('taxes.index')
            ->with('success', 'Tax rate updated successfully.');
    }

    public function destroy(Tax $tax)
    {
        if ($tax->products()->count() > 0 || $tax->services()->count() > 0) {
            return redirect()->route('taxes.index')
                ->with('error', 'Cannot delete tax rate that is being used by products or services.');
        }

        $tax->delete();

        return redirect()->route('taxes.index')
            ->with('success', 'Tax rate deleted successfully.');
    }

    public function toggle(Tax $tax)
    {
        $tax->update(['is_active' => !$tax->is_active]);

        $status = $tax->is_active ? 'activated' : 'deactivated';
        
        return redirect()->route('taxes.index')
            ->with('success', "Tax rate {$status} successfully.");
    }
}
