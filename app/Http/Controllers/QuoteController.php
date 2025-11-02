<?php

namespace App\Http\Controllers;

use App\Models\Quote;
use App\Models\QuoteItem;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Service;
use App\Models\Tax;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class QuoteController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $quotes = Quote::with(['customer', 'creator', 'items'])
            ->when($request->status, function ($query, $status) {
                return $query->where('status', $status);
            })
            ->when($request->search, function ($query, $search) {
                return $query->where(function ($q) use ($search) {
                    $q->where('quote_number', 'like', "%{$search}%")
                      ->orWhereHas('customer', function ($q) use ($search) {
                          $q->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                      });
                });
            })
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        $stats = [
            'total' => Quote::count(),
            'draft' => Quote::where('status', 'draft')->count(),
            'sent' => Quote::where('status', 'sent')->count(),
            'accepted' => Quote::where('status', 'accepted')->count(),
            'rejected' => Quote::where('status', 'rejected')->count(),
            'expired' => Quote::where('valid_until', '<', now())->count(),
        ];

        return view('quotes.index', compact('quotes', 'stats'));
    }

    public function create()
    {
        $customers = Customer::orderBy('name')->get();
        $products = Product::active()->orderBy('name')->get();
        $services = Service::active()->orderBy('name')->get();
        $taxes = Tax::active()->orderBy('name')->get();

        return view('quotes.create', compact('customers', 'products', 'services', 'taxes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'quote_date' => 'required|date',
            'valid_until' => 'required|date|after:quote_date',
            'items' => 'required|array|min:1',
            'items.*.description' => 'required|string',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.tax_rate' => 'nullable|numeric|min:0|max:100',
            'discount_amount' => 'nullable|numeric|min:0',
            'terms' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $quote = Quote::create([
            'customer_id' => $validated['customer_id'],
            'created_by' => Auth::id(),
            'quote_number' => 'QUO-' . str_pad(Quote::count() + 1, 6, '0', STR_PAD_LEFT),
            'status' => 'draft',
            'quote_date' => $validated['quote_date'],
            'valid_until' => $validated['valid_until'],
            'terms' => $validated['terms'] ?? null,
            'notes' => $validated['notes'] ?? null,
        ]);

        $subtotal = 0;
        $totalTax = 0;

        foreach ($validated['items'] as $item) {
            $lineTotal = $item['quantity'] * $item['unit_price'];
            $taxAmount = $item['tax_rate'] ? ($lineTotal * $item['tax_rate'] / 100) : 0;

            QuoteItem::create([
                'quote_id' => $quote->id,
                'description' => $item['description'],
                'quantity' => $item['quantity'],
                'unit_price' => $item['unit_price'],
                'tax_rate' => $item['tax_rate'] ?? 0,
                'tax_amount' => $taxAmount,
                'total_amount' => $lineTotal + $taxAmount,
            ]);

            $subtotal += $lineTotal;
            $totalTax += $taxAmount;
        }

        $discountAmount = $validated['discount_amount'] ?? 0;
        $totalAmount = $subtotal + $totalTax - $discountAmount;

        $quote->update([
            'subtotal' => $subtotal,
            'tax_amount' => $totalTax,
            'discount_amount' => $discountAmount,
            'total_amount' => $totalAmount,
        ]);

        return redirect()->route('quotes.show', $quote)
            ->with('success', 'Quote created successfully.');
    }

    public function show(Quote $quote)
    {
        $quote->load(['customer', 'creator', 'items.product', 'items.service']);

        return view('quotes.show', compact('quote'));
    }

    public function edit(Quote $quote)
    {
        if ($quote->status !== 'draft') {
            return redirect()->route('quotes.index')
                ->with('error', 'Only draft quotes can be edited.');
        }

        $quote->load(['customer', 'items']);
        $customers = Customer::orderBy('name')->get();
        $products = Product::active()->orderBy('name')->get();
        $services = Service::active()->orderBy('name')->get();
        $taxes = Tax::active()->orderBy('name')->get();

        return view('quotes.edit', compact('quote', 'customers', 'products', 'services', 'taxes'));
    }

    public function update(Request $request, Quote $quote)
    {
        if ($quote->status !== 'draft') {
            return redirect()->route('quotes.index')
                ->with('error', 'Only draft quotes can be updated.');
        }

        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'quote_date' => 'required|date',
            'valid_until' => 'required|date|after:quote_date',
            'items' => 'required|array|min:1',
            'items.*.description' => 'required|string',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.tax_rate' => 'nullable|numeric|min:0|max:100',
            'discount_amount' => 'nullable|numeric|min:0',
            'terms' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $quote->update([
            'customer_id' => $validated['customer_id'],
            'quote_date' => $validated['quote_date'],
            'valid_until' => $validated['valid_until'],
            'terms' => $validated['terms'] ?? null,
            'notes' => $validated['notes'] ?? null,
        ]);

        // Delete existing items
        $quote->items()->delete();

        $subtotal = 0;
        $totalTax = 0;

        foreach ($validated['items'] as $item) {
            $lineTotal = $item['quantity'] * $item['unit_price'];
            $taxAmount = $item['tax_rate'] ? ($lineTotal * $item['tax_rate'] / 100) : 0;

            QuoteItem::create([
                'quote_id' => $quote->id,
                'description' => $item['description'],
                'quantity' => $item['quantity'],
                'unit_price' => $item['unit_price'],
                'tax_rate' => $item['tax_rate'] ?? 0,
                'tax_amount' => $taxAmount,
                'total_amount' => $lineTotal + $taxAmount,
            ]);

            $subtotal += $lineTotal;
            $totalTax += $taxAmount;
        }

        $discountAmount = $validated['discount_amount'] ?? 0;
        $totalAmount = $subtotal + $totalTax - $discountAmount;

        $quote->update([
            'subtotal' => $subtotal,
            'tax_amount' => $totalTax,
            'discount_amount' => $discountAmount,
            'total_amount' => $totalAmount,
        ]);

        return redirect()->route('quotes.show', $quote)
            ->with('success', 'Quote updated successfully.');
    }

    public function destroy(Quote $quote)
    {
        if ($quote->status !== 'draft') {
            return redirect()->route('quotes.index')
                ->with('error', 'Only draft quotes can be deleted.');
        }

        $quote->delete();

        return redirect()->route('quotes.index')
            ->with('success', 'Quote deleted successfully.');
    }

    public function send(Quote $quote)
    {
        if (!in_array($quote->status, ['draft', 'sent'])) {
            return redirect()->route('quotes.index')
                ->with('error', 'Quote cannot be sent in its current status.');
        }

        $quote->update(['status' => 'sent']);

        return redirect()->route('quotes.show', $quote)
            ->with('success', 'Quote sent successfully.');
    }

    public function convertToInvoice(Quote $quote)
    {
        if ($quote->status !== 'accepted') {
            return redirect()->route('quotes.show', $quote)
                ->with('error', 'Only accepted quotes can be converted to invoices.');
        }

        $invoice = $quote->convertToInvoice();

        return redirect()->route('invoices.show', $invoice)
            ->with('success', 'Quote converted to invoice successfully.');
    }

    public function accept(Quote $quote)
    {
        if ($quote->status !== 'sent') {
            return redirect()->route('quotes.show', $quote)
                ->with('error', 'Only sent quotes can be accepted.');
        }

        if ($quote->is_expired) {
            return redirect()->route('quotes.show', $quote)
                ->with('error', 'Cannot accept expired quote.');
        }

        $quote->update(['status' => 'accepted']);

        return redirect()->route('quotes.show', $quote)
            ->with('success', 'Quote accepted successfully.');
    }

    public function reject(Quote $quote)
    {
        if ($quote->status !== 'sent') {
            return redirect()->route('quotes.show', $quote)
                ->with('error', 'Only sent quotes can be rejected.');
        }

        $quote->update(['status' => 'rejected']);

        return redirect()->route('quotes.show', $quote)
            ->with('success', 'Quote rejected successfully.');
    }
}
