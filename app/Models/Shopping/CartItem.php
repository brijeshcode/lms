<?php

namespace App\Models\Shopping;

use App\Models\Sale\Packager\Package;
use App\Traits\Authorable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    use HasFactory;
    use Authorable;
    protected $fillable = ['cart_id','product_id', 'product_title', 'quantity','regular_price', 'sell_price'];

    public function product()
    {
        return $this->belongsTo(Package::class);
    }
}