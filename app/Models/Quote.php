<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quote extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'created_by',
        'quote_number',
        'status',
        'quote_date',
        'valid_until',
        'subtotal',
        'tax_amount',
        'discount_amount',
        'total_amount',
        'terms',
        'notes',
    ];

    protected $casts = [
        'customer_id' => 'integer',
        'created_by' => 'integer',
        'quote_date' => 'date',
        'valid_until' => 'date',
        'subtotal' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'status' => 'string',
    ];

    // Relationships
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function items()
    {
        return $this->hasMany(QuoteItem::class);
    }

    // Scopes
    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    public function scopeSent($query)
    {
        return $query->where('status', 'sent');
    }

    public function scopeAccepted($query)
    {
        return $query->where('status', 'accepted');
    }

    public function scopeExpired($query)
    {
        return $query->where('valid_until', '<', now());
    }

    // Accessors
    public function getFormattedTotalAttribute()
    {
        return number_format($this->total_amount, 2);
    }

    public function getIsExpiredAttribute()
    {
        return $this->valid_until < now();
    }

    public function getDaysUntilExpiryAttribute()
    {
        return now()->diffInDays($this->valid_until, false);
    }

    public function convertToInvoice()
    {
        $invoice = Invoice::create([
            'customer_id' => $this->customer_id,
            'created_by' => $this->created_by,
            'invoice_number' => 'INV-' . str_pad(rand(1, 9999), 6, '0', STR_PAD_LEFT),
            'status' => 'draft',
            'issue_date' => now()->toDateString(),
            'due_date' => now()->addDays(30)->toDateString(),
            'subtotal' => $this->subtotal,
            'tax_amount' => $this->tax_amount,
            'discount_amount' => $this->discount_amount,
            'total_amount' => $this->total_amount,
            'paid_amount' => 0,
            'balance_amount' => $this->total_amount,
            'notes' => 'Converted from quote ' . $this->quote_number,
        ]);

        foreach ($this->items as $item) {
            InvoiceItem::create([
                'invoice_id' => $invoice->id,
                'product_id' => $item->product_id,
                'service_id' => $item->service_id,
                'description' => $item->description,
                'quantity' => $item->quantity,
                'unit_price' => $item->unit_price,
                'tax_rate' => $item->tax_rate,
                'tax_amount' => $item->tax_amount,
                'total_amount' => $item->total_amount,
            ]);
        }

        $this->update(['status' => 'accepted']);

        return $invoice;
    }
}
