<?php

namespace App\Models\Sale\Orders;

use App\Models\Sale\Orders\OrderItem;
use App\Models\User;
use App\Traits\Authorable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use HasFactory;
    use SoftDeletes;
    use Authorable;
    protected $fillable = [ 'date', 'customer_id', 'quantity','discount', 'sub_total', 'total', 'status', 'is_paid', 'note', 'active'];

    protected $casts = [
      'active' => 'boolean',
    ];

    public function products()
    {
      return $this->hasMany(OrderItem::class);
    }

    public function customer()
    {
      return $this->belongsTo(User::class, 'customer_id');
    }
}
