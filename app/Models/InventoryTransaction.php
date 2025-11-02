<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'user_id',
        'type',
        'quantity',
        'previous_stock',
        'new_stock',
        'unit_cost',
        'reference_type',
        'reference_id',
        'notes',
    ];

    protected $casts = [
        'product_id' => 'integer',
        'user_id' => 'integer',
        'quantity' => 'integer',
        'previous_stock' => 'integer',
        'new_stock' => 'integer',
        'unit_cost' => 'decimal:2',
        'reference_id' => 'integer',
        'type' => 'string',
    ];

    // Relationships
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function reference()
    {
        return $this->morphTo();
    }

    // Scopes
    public function scopePurchases($query)
    {
        return $query->where('type', 'purchase');
    }

    public function scopeSales($query)
    {
        return $query->where('type', 'sale');
    }

    public function scopeAdjustments($query)
    {
        return $query->where('type', 'adjustment');
    }

    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    // Accessors
    public function getFormattedQuantityAttribute()
    {
        return $this->type === 'sale' ? '-' . $this->quantity : '+' . $this->quantity;
    }

    public function getFormattedCostAttribute()
    {
        return $this->unit_cost ? number_format($this->unit_cost, 2) : 'N/A';
    }

    public function getTotalCostAttribute()
    {
        return $this->unit_cost ? $this->quantity * $this->unit_cost : 0;
    }

    public function getFormattedTotalCostAttribute()
    {
        return number_format($this->total_cost, 2);
    }

    public function getTypeColorAttribute()
    {
        $colors = [
            'purchase' => 'success',
            'sale' => 'danger',
            'adjustment' => 'warning',
            'return' => 'info',
            'transfer' => 'secondary',
        ];

        return $colors[$this->type] ?? 'primary';
    }
}
