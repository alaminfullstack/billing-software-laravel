<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Service extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'name',
        'description',
        'hourly_rate',
        'duration',
        'duration_unit',
        'basic_price',
        'standard_price',
        'premium_price',
        'features',
        'is_bookable',
        'is_active',
    ];

    protected $casts = [
        'category_id' => 'integer',
        'hourly_rate' => 'decimal:2',
        'basic_price' => 'decimal:2',
        'standard_price' => 'decimal:2',
        'premium_price' => 'decimal:2',
        'duration' => 'integer',
        'duration_unit' => 'string',
        'features' => 'array',
        'is_bookable' => 'boolean',
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

    public function scopeBookable($query)
    {
        return $query->where('is_bookable', true);
    }

    // Accessors
    public function getFormattedRateAttribute()
    {
        return number_format($this->hourly_rate, 2);
    }

    public function getDurationTextAttribute()
    {
        return $this->duration . ' ' . Str::plural($this->duration_unit, $this->duration);
    }

    public function getPriceRangeAttribute()
    {
        $prices = array_filter([
            $this->basic_price,
            $this->standard_price,
            $this->premium_price
        ]);

        if (empty($prices)) {
            return '$' . number_format($this->hourly_rate, 2);
        }

        $min = min($prices);
        $max = max($prices);

        return $min == $max ? '$' . number_format($min, 2) : '$' . number_format($min, 2) . ' - $' . number_format($max, 2);
    }
}
