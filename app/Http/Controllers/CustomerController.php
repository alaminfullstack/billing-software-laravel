<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $query = Customer::where('user_id', Auth::id())->with('user');

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        if ($request->has('customer_type') && $request->customer_type != '') {
            $query->where('customer_type', $request->customer_type);
        }

        $customers = $query->orderBy('created_at', 'desc')
                          ->paginate(15)
                          ->withQueryString();

        return view('customers.index', compact('customers'));
    }

    public function show(Customer $customer)
    {
        Gate::authorize('view', $customer);
        
        $customer->load('user', 'invoices.payments');
        
        return view('customers.show', compact('customer'));
    }

    public function create()
    {
        Gate::authorize('create', Customer::class);
        
        return view('customers.create');
    }

    public function store(Request $request)
    {
        Gate::authorize('create', Customer::class);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:customers',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'zip_code' => 'nullable|string|max:20',
            'tax_number' => 'nullable|string|max:50',
            'customer_type' => 'required|in:individual,business',
            'status' => 'required|in:active,inactive,blocked',
            'notes' => 'nullable|string',
        ]);

        $validated['user_id'] = Auth::id();

        $customer = Customer::create($validated);

        return redirect()->route('customers.index')
                        ->with('success', 'Customer created successfully.');
    }

    public function edit(Customer $customer)
    {
        Gate::authorize('update', $customer);
        
        return view('customers.edit', compact('customer'));
    }

    public function update(Request $request, Customer $customer)
    {
        Gate::authorize('update', $customer);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('customers')->ignore($customer->id)],
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'zip_code' => 'nullable|string|max:20',
            'tax_number' => 'nullable|string|max:50',
            'customer_type' => 'required|in:individual,business',
            'status' => 'required|in:active,inactive,blocked',
            'notes' => 'nullable|string',
        ]);

        $customer->update($validated);

        return redirect()->route('customers.index')
                        ->with('success', 'Customer updated successfully.');
    }

    public function destroy(Customer $customer)
    {
        Gate::authorize('delete', $customer);

        $customer->delete();

        return redirect()->route('customers.index')
                        ->with('success', 'Customer deleted successfully.');
    }
}
