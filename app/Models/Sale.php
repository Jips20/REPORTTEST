<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Sale extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'product_id', 'user_id', 'quantity', 'unit_cost', 'total_price'
    ];

    public function seller()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // withTrashed() so sales of deleted products still show up in reports
    public function product()
    {
        return $this->belongsTo(Product::class)->withTrashed();
    }

    // Total cost of this sale = cost per unit x quantity
    public function getTotalCostAttribute()
    {
        return round($this->unit_cost * $this->quantity, 2);
    }

    // Profit of this sale = what the customer paid - what it cost you
    public function getProfitAttribute()
    {
        return round($this->total_price - $this->total_cost, 2);
    }
}