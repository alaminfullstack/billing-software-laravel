<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
        'city',
        'state',
        'country',
        'zip_code',
        'tax_number',
        'website',
        'notes',
        'status',
    ];

    protected $casts = [
        'status' => 'string',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function expenses()
    {
        return $this->hasMany(Expense::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeBusiness($query)
    {
        return $query->where('tax_number', '!=', null);
    }

    // Accessors
    public function getFullAddressAttribute()
    {
        $address = [];
        
        if ($this->address) $address[] = $this->address;
        if ($this->city) $address[] = $this->city;
        if ($this->state) $address[] = $this->state;
        if ($this->zip_code) $address[] = $this->zip_code;
        if ($this->country) $address[] = $this->country;
        
        return implode(', ', $address);
    }

    public function getTotalExpensesAttribute()
    {
        return $this->expenses()->sum('amount');
    }

    public function getExpenseCountAttribute()
    {
        return $this->expenses()->count();
    }
}
