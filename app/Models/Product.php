<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'sku',
        'name',
        'price',
        'stock',
        'minimum_stock',
        'unit',
        'description',
    ];

    /**
     * Relationship: Product belongs to Category
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Relationship: Product belongs to many Suppliers (Many-to-Many)
     */
    public function suppliers()
    {
        return $this->belongsToMany(Supplier::class, 'product_suppliers')
                    ->withPivot('supply_price', 'lead_time_days')
                    ->withTimestamps();
    }

    /**
     * Relationship: Product has many StockMovements
     */
    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class);
    }
}
