<?php

namespace App\Models\Sale\Orders;

use App\Models\Sale\Orders\Order;
use App\Traits\Authorable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderItem extends Model
{
    use HasFactory;
    use SoftDeletes;
    use Authorable;
    protected $fillable = [ 'order_id', 'product_id', 'product_title', 'quantity', 'regular_price', 'sell_price'];

    protected $casts = [
      'active' => 'boolean',
    ];

    public function order()
    {
      return $this->belongsTo(Order::class);
    }
}
