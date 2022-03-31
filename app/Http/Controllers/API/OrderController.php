<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Sale\Orders\Order;
use App\Models\Shopping\Cart;
use App\Models\User;
use App\Traits\ApiResponsable;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    use ApiResponsable;

    public function studnetCheck($studentId)
    {
        return $user = User::where('is_student', 1)->whereId($studentId)->whereActive(1)->first();
    }

    public function create($customer_id)
    {
        $user = $this->studnetCheck($customer_id);
        if (is_null($user)) {
            return  $this->apiResponse(403, 'fail', $customer_id. ' is not valid student id.');
        }
        $cart = Cart::whereCustomerId($customer_id)->with('items')->first();
        $orderData = [
            'customer_id' => $customer_id,
            'date' => now(),
            'quantity' => $cart->quantity,
            'discount' => 0,
            'sub_total' => $cart->sub_total,
            'total'=> $cart->total,
            'status' => 'processing',
            'is_paid'=> false,
            'note' => " order created by api "
        ];
        $order = Order::create($orderData)->products()->createMany(collect($cart->items)->toArray());

        if (!is_null($order)) {
            $cart->items()->delete();
            $cart->delete();
            return  $this->apiResponse(200, 'success', 'Order Placed Successfuly');
        }

        return  $this->apiResponse(500, 'error', 'cart data not found');

    }
}
