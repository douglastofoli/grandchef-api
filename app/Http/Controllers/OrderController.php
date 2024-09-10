<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'products' => 'required|array',
            'products.*.product_id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1',
            'products.*.price' => 'required|numeric'
        ]);

        $order = Order::create(['status' => 'open']);
        $totalPrice = 0;

        foreach ($request->products as $item) {
            $product = Product::find($item['product_id']);
            $order->products()->attach($product->id, [
                'price' => $item['price'],
                'quantity' => $item['quantity']
            ]);
            $totalPrice += $item['price'] * $item['quantity'];
        }

        $order->update(['total_price' => $totalPrice]);

        return response()->json($order->load('products'), 201);
    }

    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 10); 

        $orders = Order::with(['products' => function($query) {
            $query->select('products.id', 'products.name', 'order_product.price', 'order_product.quantity');
        }])->paginate($perPage);

        $orders->getCollection()->transform(function($order) {
            return [
                'id' => $order->id,
                'status' => $order->status,
                'total_price' => $order->total_price,
                'products' => $order->products->map(function($product) {
                    return [
                        'product_id' => $product->id,
                        'name' => $product->name,
                        'price' => $product->pivot->price,
                        'quantity' => $product->pivot->quantity
                    ];
                })
            ];
        });

        return response()->json($orders);
    }

    public function show($id)
    {
        $order = Order::with(['products' => function($query) {
            $query->select('products.id', 'products.name', 'order_product.price', 'order_product.quantity');
        }])->findOrFail($id);

        $formattedOrder = [
            'id' => $order->id,
            'status' => $order->status,
            'total_price' => $order->total_price,
            'products' => $order->products->map(function($product) {
                return [
                    'product_id' => $product->id,
                    'name' => $product->name,
                    'price' => $product->pivot->price,
                    'quantity' => $product->pivot->quantity
                ];
            })
        ];

        return response()->json($formattedOrder);
    }
    
    public function update(Request $request, $id)
    {        
        $validator = Validator::make($request->input(), [
            'status' => 'required|in:open,approved,completed,canceled'        
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors()
            ]);
        }

        $order = Order::findOrFail($id);
        $order->update(['status' => $request->status]);

        return response()->json($order);
    }
}
