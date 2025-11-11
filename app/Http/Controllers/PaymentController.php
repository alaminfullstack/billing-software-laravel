<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Carbon\Carbon;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $query = Payment::where('created_by', Auth::id())->with('invoice.customer');

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('reference_number', 'like', "%{$search}%")
                  ->orWhereHas('invoice', function ($invoiceQuery) use ($search) {
                      $invoiceQuery->where('invoice_number', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->has('payment_method') && $request->payment_method != '') {
            $query->where('payment_method', $request->payment_method);
        }

        if ($request->has('date_from')) {
            $query->where('payment_date', '>=', $request->date_from);
        }

        if ($request->has('date_to')) {
            $query->where('payment_date', '<=', $request->date_to);
        }

        $payments = $query->orderBy('payment_date', 'desc')
                         ->paginate(15)
                         ->withQueryString();

        return view('payments.index', compact('payments'));
    }

    public function create(Request $request, Invoice $invoice = null)
    {
        Gate::authorize('create', Payment::class);

        $invoices = Invoice::where('created_by', Auth::id())
                          ->where('balance_amount', '>', 0)
                          ->whereIn('status', ['sent', 'overdue'])
                          ->with('customer')
                          ->orderBy('invoice_number')
                          ->get();

        $selectedInvoice = $invoice;

        return view('payments.create', compact('invoices', 'selectedInvoice'));
    }

    public function store(Request $request)
    {
        Gate::authorize('create', Payment::class);

        $validated = $request->validate([
            'invoice_id' => 'required|exists:invoices,id',
            'payment_date' => 'required|date',
            'amount' => 'required|numeric|min:0.01',
            'payment_method' => 'required|in:cash,bank_transfer,credit_card,check,online,other',
            'reference_number' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        DB::beginTransaction();
        
        try {
            $invoice = Invoice::findOrFail($validated['invoice_id']);
            
            // Check if payment amount doesn't exceed balance
            if ($validated['amount'] > $invoice->balance_amount) {
                return back()->with('error', 'Payment amount cannot exceed invoice balance.');
            }
            
            // Create payment
            $payment = Payment::create([
                'invoice_id' => $validated['invoice_id'],
                'created_by' => Auth::id(),
                'payment_date' => $validated['payment_date'],
                'amount' => $validated['amount'],
                'payment_method' => $validated['payment_method'],
                'reference_number' => $validated['reference_number'],
                'notes' => $validated['notes'],
            ]);
            
            // Update invoice
            $newPaidAmount = $invoice->paid_amount + $validated['amount'];
            $newBalance = $invoice->total_amount - $newPaidAmount;
            
            $status = 'sent';
            if ($newBalance <= 0) {
                $status = 'paid';
                $newBalance = 0;
            } elseif ($newPaidAmount > 0) {
                $status = 'sent'; // Keep as sent for partial payments
            }
            
            $invoice->update([
                'paid_amount' => $newPaidAmount,
                'balance_amount' => $newBalance,
                'status' => $status,
            ]);
            
            DB::commit();
            
            return redirect()->route('payments.show', $payment)
                           ->with('success', 'Payment recorded successfully.');
                           
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Failed to record payment. Please try again.');
        }
    }

    public function show(Payment $payment)
    {
        Gate::authorize('view', $payment);
        
        $payment->load('invoice.customer', 'creator');
        
        return view('payments.show', compact('payment'));
    }

    public function edit(Payment $payment)
    {
        $this->authorize('update', $payment);
        
        $payment->load('invoice.customer');
        
        return view('payments.edit', compact('payment'));
    }

    public function update(Request $request, Payment $payment)
    {
        $this->authorize('update', $payment);

        $validated = $request->validate([
            'payment_date' => 'required|date',
            'amount' => 'required|numeric|min:0.01',
            'payment_method' => 'required|in:cash,bank_transfer,credit_card,check,online,other',
            'reference_number' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        DB::beginTransaction();
        
        try {
            // Calculate the difference
            $oldAmount = $payment->amount;
            $newAmount = $validated['amount'];
            $amountDifference = $newAmount - $oldAmount;
            
            // Update payment
            $payment->update([
                'payment_date' => $validated['payment_date'],
                'amount' => $validated['amount'],
                'payment_method' => $validated['payment_method'],
                'reference_number' => $validated['reference_number'],
                'notes' => $validated['notes'],
            ]);
            
            // Update invoice
            $invoice = $payment->invoice;
            $newPaidAmount = $invoice->paid_amount + $amountDifference;
            $newBalance = $invoice->total_amount - $newPaidAmount;
            
            if ($newPaidAmount < 0 || $newBalance < 0) {
                DB::rollback();
                return back()->with('error', 'Invalid payment amount.');
            }
            
            $status = 'sent';
            if ($newBalance <= 0) {
                $status = 'paid';
                $newBalance = 0;
            } elseif ($newPaidAmount > 0) {
                $status = 'sent';
            }
            
            $invoice->update([
                'paid_amount' => $newPaidAmount,
                'balance_amount' => $newBalance,
                'status' => $status,
            ]);
            
            DB::commit();
            
            return redirect()->route('payments.show', $payment)
                           ->with('success', 'Payment updated successfully.');
                           
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Failed to update payment. Please try again.');
        }
    }

    public function destroy(Payment $payment)
    {
        $this->authorize('delete', $payment);

        DB::beginTransaction();
        
        try {
            $invoice = $payment->invoice;
            
            // Update invoice
            $newPaidAmount = $invoice->paid_amount - $payment->amount;
            $newBalance = $invoice->total_amount - $newPaidAmount;
            
            $status = 'sent';
            if ($newPaidAmount <= 0) {
                $status = 'sent';
                $newBalance = $invoice->total_amount;
            }
            
            $invoice->update([
                'paid_amount' => $newPaidAmount,
                'balance_amount' => $newBalance,
                'status' => $status,
            ]);
            
            // Delete payment
            $payment->delete();
            
            DB::commit();
            
            return redirect()->route('payments.index')
                           ->with('success', 'Payment deleted successfully.');
                           
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Failed to delete payment. Please try again.');
        }
    }
}
