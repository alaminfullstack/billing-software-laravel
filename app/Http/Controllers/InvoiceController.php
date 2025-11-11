<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Service;
use App\Models\InvoiceItem;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class InvoiceController extends Controller
{
    public function index(Request $request)
    {
        $query = Invoice::where('created_by', Auth::id())->with('customer');

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('invoice_number', 'like', "%{$search}%")
                  ->orWhereHas('customer', function ($customerQuery) use ($search) {
                      $customerQuery->where('name', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        if ($request->has('date_from')) {
            $query->where('issue_date', '>=', $request->date_from);
        }

        if ($request->has('date_to')) {
            $query->where('issue_date', '<=', $request->date_to);
        }

        $invoices = $query->orderBy('created_at', 'desc')
                         ->paginate(15)
                         ->withQueryString();

        return view('invoices.index', compact('invoices'));
    }

    public function show(Invoice $invoice)
    {
        Gate::authorize('view', $invoice);
        
        $invoice->load('customer', 'items.product', 'items.service', 'payments');
        
        return view('invoices.show', compact('invoice'));
    }

    public function create()
    {
        Gate::authorize('create', Invoice::class);
        
        $customers = Customer::where('user_id', Auth::id())
                            ->where('status', 'active')
                            ->orderBy('name')
                            ->get();
        
        $products = Product::all();
        $services = Service::all();

        return view('invoices.create', compact('customers', 'products', 'services'));
    }

    public function store(Request $request)
    {
        Gate::authorize('create', Invoice::class);

        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'issue_date' => 'required|date',
            'due_date' => 'required|date|after:issue_date',
            'invoice_items' => 'required|array|min:1',
            'invoice_items.*.description' => 'required|string',
            'invoice_items.*.quantity' => 'required|numeric|min:0.01',
            'invoice_items.*.unit_price' => 'required|numeric|min:0',
            'invoice_items.*.tax_rate' => 'nullable|numeric|min:0|max:100',
            'discount_amount' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        DB::beginTransaction();
        
        try {
            // Generate invoice number
            $invoiceNumber = $this->generateInvoiceNumber();
            
            // Calculate totals
            $subtotal = 0;
            $taxAmount = 0;
            
            foreach ($validated['invoice_items'] as $item) {
                $itemSubtotal = $item['quantity'] * $item['unit_price'];
                $itemTax = $itemSubtotal * (($item['tax_rate'] ?? 0) / 100);
                
                $subtotal += $itemSubtotal;
                $taxAmount += $itemTax;
            }
            
            $totalAmount = $subtotal + $taxAmount - ($validated['discount_amount'] ?? 0);
            
            // Create invoice
            $invoice = Invoice::create([
                'customer_id' => $validated['customer_id'],
                'created_by' => Auth::id(),
                'invoice_number' => $invoiceNumber,
                'issue_date' => $validated['issue_date'],
                'due_date' => $validated['due_date'],
                'subtotal' => $subtotal,
                'tax_amount' => $taxAmount,
                'discount_amount' => $validated['discount_amount'] ?? 0,
                'total_amount' => $totalAmount,
                'balance_amount' => $totalAmount,
                'status' => 'draft',
                'notes' => $validated['notes'] ?? null,
            ]);
            
            // Create invoice items
            foreach ($validated['invoice_items'] as $item) {
                $itemSubtotal = $item['quantity'] * $item['unit_price'];
                $itemTax = $itemSubtotal * (($item['tax_rate'] ?? 0) / 100);
                $itemTotal = $itemSubtotal + $itemTax;
                
                InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'product_id' => $item['product_id'] ?? null,
                    'service_id' => $item['service_id'] ?? null,
                    'description' => $item['description'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'tax_rate' => $item['tax_rate'] ?? 0,
                    'tax_amount' => $itemTax,
                    'total_amount' => $itemTotal,
                ]);
            }
            
            DB::commit();
            
            return redirect()->route('invoices.show', $invoice)
                           ->with('success', 'Invoice created successfully.');
                           
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Failed to create invoice. Please try again.');
        }
    }

    public function edit(Invoice $invoice)
    {
        Gate::authorize('update', $invoice);
        
        if ($invoice->status !== 'draft') {
            return back()->with('error', 'Only draft invoices can be edited.');
        }
        
        $invoice->load('items.product', 'items.service');
        $customers = Customer::where('user_id', Auth::id())
                            ->where('status', 'active')
                            ->orderBy('name')
                            ->get();
        
        $products = Product::all();
        $services = Service::all();

        return view('invoices.edit', compact('invoice', 'customers', 'products', 'services'));
    }

    public function update(Request $request, Invoice $invoice)
    {
        Gate::authorize('update', $invoice);

        if ($invoice->status !== 'draft') {
            return back()->with('error', 'Only draft invoices can be updated.');
        }

        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'issue_date' => 'required|date',
            'due_date' => 'required|date|after:issue_date',
            'invoice_items' => 'required|array|min:1',
            'invoice_items.*.description' => 'required|string',
            'invoice_items.*.quantity' => 'required|numeric|min:0.01',
            'invoice_items.*.unit_price' => 'required|numeric|min:0',
            'invoice_items.*.tax_rate' => 'nullable|numeric|min:0|max:100',
            'discount_amount' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        DB::beginTransaction();
        
        try {
            // Calculate totals
            $subtotal = 0;
            $taxAmount = 0;
            
            foreach ($validated['invoice_items'] as $item) {
                $itemSubtotal = $item['quantity'] * $item['unit_price'];
                $itemTax = $itemSubtotal * (($item['tax_rate'] ?? 0) / 100);
                
                $subtotal += $itemSubtotal;
                $taxAmount += $itemTax;
            }
            
            $totalAmount = $subtotal + $taxAmount - ($validated['discount_amount'] ?? 0);
            
            // Update invoice
            $invoice->update([
                'customer_id' => $validated['customer_id'],
                'issue_date' => $validated['issue_date'],
                'due_date' => $validated['due_date'],
                'subtotal' => $subtotal,
                'tax_amount' => $taxAmount,
                'discount_amount' => $validated['discount_amount'] ?? 0,
                'total_amount' => $totalAmount,
                'balance_amount' => $totalAmount,
                'notes' => $validated['notes'] ?? null,
            ]);
            
            // Delete existing items and recreate
            $invoice->items()->delete();
            
            foreach ($validated['invoice_items'] as $item) {
                $itemSubtotal = $item['quantity'] * $item['unit_price'];
                $itemTax = $itemSubtotal * (($item['tax_rate'] ?? 0) / 100);
                $itemTotal = $itemSubtotal + $itemTax;
                
                InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'product_id' => $item['product_id'] ?? null,
                    'service_id' => $item['service_id'] ?? null,
                    'description' => $item['description'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'tax_rate' => $item['tax_rate'] ?? 0,
                    'tax_amount' => $itemTax,
                    'total_amount' => $itemTotal,
                ]);
            }
            
            DB::commit();
            
            return redirect()->route('invoices.show', $invoice)
                           ->with('success', 'Invoice updated successfully.');
                           
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Failed to update invoice. Please try again.');
        }
    }

    public function destroy(Invoice $invoice)
    {
        Gate::authorize('delete', $invoice);

        if ($invoice->status !== 'draft') {
            return back()->with('error', 'Only draft invoices can be deleted.');
        }

        $invoice->delete();

        return redirect()->route('invoices.index')
                        ->with('success', 'Invoice deleted successfully.');
    }

    public function send(Invoice $invoice)
    {
        Gate::authorize('send', $invoice);

        if ($invoice->status === 'draft') {
            $invoice->update(['status' => 'sent']);
            return back()->with('success', 'Invoice sent successfully.');
        }

        return back()->with('error', 'Invoice cannot be sent.');
    }

    public function approve(Invoice $invoice)
    {
        Gate::authorize('update', $invoice);

        if ($invoice->status !== 'draft') {
            return back()->with('error', 'Only draft invoices can be approved.');
        }

        $invoice->update(['status' => 'sent']);

        return back()->with('success', 'Invoice approved successfully.');
    }

    public function markAsPaid(Invoice $invoice)
    {
        Gate::authorize('markAsPaid', $invoice);

        $invoice->update([
            'status' => 'paid',
            'paid_amount' => $invoice->total_amount,
            'balance_amount' => 0,
        ]);

        return back()->with('success', 'Invoice marked as paid successfully.');
    }

    private function generateInvoiceNumber()
    {
        $lastInvoice = Invoice::where('created_by', Auth::id())
                             ->orderBy('id', 'desc')
                             ->first();
        
        if ($lastInvoice) {
            $number = intval(substr($lastInvoice->invoice_number, -6)) + 1;
        } else {
            $number = 1;
        }
        
        return 'INV-' . str_pad($number, 6, '0', STR_PAD_LEFT);
    }
}
