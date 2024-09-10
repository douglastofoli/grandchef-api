<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = ['status', 'total_price'];

    public function products()
    {
        return $this->belongsToMany(Product::class, 'order_product')->withPivot('price', 'quantity');
    }

    public function calculateTotalPrice()
    {
        return $this->products->sum(function($product) {
            return $product->pivot->price * $product->pivot->quantity;
        });
    }    
}
