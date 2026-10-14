<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'contact_person',
        'phone',
        'email',
        'address',
    ];

    /**
     * Relationship: Supplier belongs to many Products
     */
    public function products()
    {
        return $this->belongsToMany(Product::class, 'product_suppliers')
                    ->withPivot('supply_price', 'lead_time_days')
                    ->withTimestamps();
    }
}
