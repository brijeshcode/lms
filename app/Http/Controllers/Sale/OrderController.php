<?php

namespace App\Http\Controllers\Sale;

use App\Http\Controllers\Controller;
use App\Models\Sale\Orders\Order;
use App\Models\Sale\Orders\OrderItem;
use App\Models\Sale\Packager\Package;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $orders = Order::select('id', 'customer_id', 'quantity','discount', 'sub_total', 'total', 'status', 'is_paid', 'note', 'active')
            ->with('customer')
            ->when($request->search, function ($query, $search){
                $query->orWhere('total', 'like', '%'. $search . '%');
                $query->orWhere('status', 'like', '%'. $search . '%');
                $query->orWhere('quantity', 'like', '%'. $search . '%');
                $query->orWhere('note', 'like', '%'. $search . '%');
            })
            ->orderBy('id', 'desc')
            ->paginate(10)
            ->withQueryString()
            ;
        return Inertia::render('Sale/Order/Index' , compact('orders'));
    }

    public function create()
    {
        $customers = User::role('customer')->whereActive(1)->get() ;
        $packages = Package::whereActive(1)->get();
        return Inertia::render('Sale/Order/Create', compact('customers', 'packages'));
    }

    public function store(Request $request)
    {
        $this->validateFull($request);
        \DB::transaction(function() use ($request) {
            Order::create($request->only('customer_id', 'date', 'quantity','discount', 'sub_total', 'total', 'status', 'is_paid', 'note', 'active'))->products()->createMany($request->products);
        });
        return redirect(route('order.index'))->with('type', 'success')->with('message', 'Order added successfully !!');
    }

    public function edit(Order $order)
    {
        $order->load('products');
        $customers = User::role('customer')->whereActive(1)->get() ;
        $packages = Package::whereActive(1)->get();
        return Inertia::render('Sale/Order/Create', compact('customers', 'order', 'packages'));
    }

    public function update(Request $request , Order $order)
    {
        $this->validateFull($request);
        \DB::transaction(function() use ($request, $order) {

            $currentItems = collect($request->products)->where('id', '!=', '');
            $currentItems->map(function ($item){
                OrderItem::whereId($item['id'])
                    ->update(collect($item)
                        ->only('order_id', 'product_id', 'product_title', 'quantity', 'regular_price', 'sell_price')
                        ->toArray()
                    );
            });

            // delete removed order item
            $order->products()->whereNotIn('id', $currentItems->pluck('id'))->delete();
            // create new order item
            $order->products()->createMany(collect($request->products)->where('id' ,'' ));

            $order->update($request->only('customer_id', 'date', 'quantity','discount', 'sub_total', 'total', 'status', 'is_paid', 'note', 'active'));
        });
        return redirect(route('order.index'))->with('type', 'success')->with('message', 'Order updated successfully !!');
    }

    public function destroy(Order $order)
    {
        $order->products()->delete();
        $order->delete();
        return redirect(route('order.index'))->with('type', 'success')->with('message', 'Order Deleteted successfully !!');

    }
    private function validateFull($request)
    {
        $tempName = 'Package';
        $request->validate(
            [
                'date' => 'required|date',
                'status' => "required",
                'customer_id' => "required|numeric",
                'products' => "array|min:1",
                'products.*.product_id' => "required",
                'products.*.quantity' => "required|numeric|min:0",
            ],
            [
                'status.required' => 'Status required.',
                'title.min' => "Invalid {$tempName}.",
                'customer_id.required' => ' Customer required.',
                'products.min' => ' No product added.',
                'products.*.product_id.required' => 'Product not selected',
                'products.*.quantity.required' => 'Product quantity required',
                'products.*.quantity.min' => 'Product quantity cannot be less then 0',
                'products.*.quantity.numeric' => 'Product quantity should be a number',
            ]
        );
    }
}
