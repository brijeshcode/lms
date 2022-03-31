<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Sale\Packager\Package;
use App\Models\Shopping\Cart;
use App\Models\Shopping\CartItem;
use App\Models\User;
use App\Traits\ApiResponsable;
use Illuminate\Http\Request;
use Validator;

class CartController extends Controller
{
    use ApiResponsable;
    public function index(Request $request)
    {
        return $request;
    }

    public function studnetCheck($studentId)
    {
        return $user = User::where('is_student', 1)->whereId($studentId)->whereActive(1)->first();
    }
    public function getCart($customer_id)
    {

        $user = $this->studnetCheck($customer_id);
        if (is_null($user)) {
            return  $this->apiResponse(403, 'fail', $customer_id. ' is not valid student id.');
        }

        $cart = Cart::select('id', 'customer_id', 'quantity', 'sub_total', 'total')->whereCustomerId($customer_id)->with('items:id,cart_id,product_id,product_title,quantity,regular_price,sell_price')->first();
        return  $this->apiResponse(200, 'success', 'Cart Data', $cart);

    }

    public function create(Request $request, $customer_id)
    {
        $user = $this->studnetCheck($customer_id);
        if (is_null($user)) {
            return  $this->apiResponse(403, 'fail', $customer_id. ' is not valid student id.');
        }

        $validator = Validator::make($request->all(), [
            'product_id' => 'required|integer',
            'quantity' => 'required|integer'
        ]);

        if ($validator->fails()) {
            return  $this->apiResponse(400, 'fail', 'Validation failed', $validator->errors());
        }

        $package = Package::whereId($request->product_id)->whereActive(1)->first();
        if (is_null($package)) {
            return  $this->apiResponse(400, 'fail', 'Product Not Found');
        }

        $item = array();
        $product = Package::select('title')->whereId($request->product_id)->first();
        $item = [
            'product_id' => $request->product_id,
            'quantity' => $request->quantity,
            'product_title' => $product->title,
            'regular_price' => $package->regular_price,
            'sell_price' => $package->sell_price
        ];

        $cart = Cart::whereCustomerId($customer_id)->first();
        if (is_null($cart)) {
            $cart = Cart::create([
                'customer_id' => $customer_id ,
                'cart_key'=> $request->quantity ,
                'quantity'=> $request->quantity ,
                'sub_total'=> $package->sell_price > 0 ? $package->sell_price * $request->quantity :$package->regular_price * $request->quantity ,
                'total' => $package->sell_price > 0 ? $package->sell_price * $request->quantity :$package->regular_price * $request->quantity
            ])->items()->createMany([$item]);
        }else{
            $this->updateCart($cart, $item);
        }
       return $this->getCart($customer_id);
    }

    public function updateCart($cart, $item)
    {
        $itemExistInCart = CartItem::whereCartId($cart->id)->where('product_id', $item['product_id'])->first();
        if (is_null($itemExistInCart)) {
            $cart->items()->create($item);
        }else{
            $itemExistInCart->quantity += $item['quantity'];
            $itemExistInCart->regular_price = $item['regular_price'];
            $itemExistInCart->sell_price = $item['sell_price'];
            $itemExistInCart->save();
        }
            $cart->quantity += $item['quantity'];
            $cart->sub_total += $item['sell_price'] > 0 ? $item['sell_price'] * $item['quantity'] : $item['regular_price'] * $item['quantity'];
            $cart->total = $cart->sub_total;
            $cart->save();
    }

    public function emptyCart($customer_id)
    {
        $user = $this->studnetCheck($customer_id);
        if (is_null($user)) {
            return  $this->apiResponse(403, 'fail', $customer_id. ' is not valid student id.');
        }
        $cart = Cart::whereCustomerId($customer_id)->first();
        if (is_null($cart)) return  $this->apiResponse(400, 'fail', 'cart is empty');

        $cart->items()->delete();
        $cart->delete();
        return  $this->apiResponse(200, 'success', 'Cart cleared');
    }

    public function removeItem(Request $request, $customer_id)
    {
        $user = $this->studnetCheck($customer_id);
        if (is_null($user)) {
            return  $this->apiResponse(403, 'fail', $customer_id. ' is not valid student id.');
        }

        $validator = Validator::make($request->all(), [
            'product_id' => 'required|integer',
            'quantity' => 'required|integer'
        ]);

        if ($validator->fails()) {
            return  $this->apiResponse(400, 'fail', 'Validation failed', $validator->errors());
        }
        $cart = Cart::whereCustomerId($customer_id)->first();

        if (is_null($cart)) {
            return  $this->apiResponse(400, 'fail', 'cart is empty');
        }

        $itemExistInCart = CartItem::whereCartId($cart->id)->where('product_id', $request->product_id)->first();
        if (is_null($itemExistInCart)) {
            return  $this->apiResponse(400, 'fail', 'Item not exist in cart.');
        }

        $itemExistInCart->quantity -= $request->quantity;
        $itemExistInCart->quantity <= 0 ?  $itemExistInCart->delete() : $itemExistInCart->save();

        $cart->quantity -= $request->quantity;
        $cart->sub_total -= $itemExistInCart->sell_price > 0 ?   $itemExistInCart->sell_price * $request->quantity: $itemExistInCart->regular_price * $itemExistInCart->quantity;
        $cart->total = $cart->sub_total;
        $cart->save();
       return $this->getCart($customer_id);
    }
}
