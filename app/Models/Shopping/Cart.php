<?php

namespace App\Models\Shopping;

use App\Models\Shopping\CartItem;
use App\Traits\Authorable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;
    use Authorable;
    protected $fillable = [ 'customer_id', 'cart_key',  'quantity', 'sub_total', 'total'];

    public function items()
    {
      return $this->hasMany(CartItem::class, 'cart_id');
    }
}