<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tax extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'rate',
        'type',
        'is_active',
        'description',
    ];

    protected $casts = [
        'rate' => 'decimal:2',
        'is_active' => 'boolean',
        'type' => 'string',
    ];

    // Relationships
    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function services()
    {
        return $this->hasMany(Service::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopePercentage($query)
    {
        return $query->where('type', 'percentage');
    }

    public function scopeFixed($query)
    {
        return $query->where('type', 'fixed');
    }

    // Accessors
    public function getFormattedRateAttribute()
    {
        return $this->type === 'percentage' ? $this->rate . '%' : '$' . number_format($this->rate, 2);
    }

    // Methods
    public function calculateTax($amount)
    {
        if ($this->type === 'percentage') {
            return $amount * ($this->rate / 100);
        }
        
        return $this->rate;
    }
}
