<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'name',
        'description',
        'sku',
        'barcode',
        'unit_price',
        'cost_price',
        'selling_price',
        'tax_rate',
        'track_inventory',
        'stock_quantity',
        'min_stock_level',
        'reorder_level',
        'unit',
        'image',
        'is_active',
        'low_stock_alert',
    ];

    protected $casts = [
        'category_id' => 'integer',
        'unit_price' => 'decimal:2',
        'cost_price' => 'decimal:2',
        'selling_price' => 'decimal:2',
        'tax_rate' => 'decimal:2',
        'track_inventory' => 'boolean',
        'stock_quantity' => 'integer',
        'min_stock_level' => 'integer',
        'reorder_level' => 'integer',
        'low_stock_alert' => 'integer',
        'is_active' => 'boolean',
    ];

    // Relationships
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function invoiceItems()
    {
        return $this->hasMany(InvoiceItem::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeInStock($query)
    {
        return $query->where('stock_quantity', '>', 0);
    }

    public function scopeLowStock($query)
    {
        return $query->whereColumn('stock_quantity', '<=', 'low_stock_alert');
    }

    // Accessors
    public function getFormattedPriceAttribute()
    {
        return number_format($this->unit_price, 2);
    }

    public function getProfitMarginAttribute()
    {
        if (!$this->cost_price || $this->cost_price <= 0) {
            return 0;
        }
        return (($this->unit_price - $this->cost_price) / $this->cost_price) * 100;
    }

    public function getStockStatusAttribute()
    {
        if ($this->stock_quantity <= 0) {
            return 'Out of Stock';
        } elseif ($this->stock_quantity <= $this->low_stock_alert) {
            return 'Low Stock';
        } else {
            return 'In Stock';
        }
    }

    public function getStockStatusColorAttribute()
    {
        if ($this->stock_quantity <= 0) {
            return 'danger';
        } elseif ($this->stock_quantity <= $this->low_stock_alert) {
            return 'warning';
        } else {
            return 'success';
        }
    }

    public function getTotalValueAttribute()
    {
        return $this->stock_quantity * $this->cost_price;
    }
}
