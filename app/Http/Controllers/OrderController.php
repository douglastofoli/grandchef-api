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
        $validator = Validator::make($request->input(), [
            'products' => 'required|array',
            'products.*.product_id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1',
            'products.*.price' => 'required|numeric'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors()
            ], 400);
        }

        $order = Order::create(['status' => 'open']);

        foreach ($request->products as $item) {
            $product = Product::with('category')->find($item['product_id']);
            $order->products()->attach($product->id, [
                'price' => $item['price'],
                'quantity' => $item['quantity']
            ]);
        }
    
        $totalPrice = $order->calculateTotalPrice();

        $order->update(['total_price' => $totalPrice]);

        $orderProducts = $order->products->map(function($product) {
            return [
                'product_id' => $product->id,
                'product' => [
                    'name' => $product->name  
                ],
                'category' => [
                    'name' => $product->category->name
                ],
                'quantity' => $product->pivot->quantity,
                'price' => $product->price
            ];
        });

        return response()->json([
            'id' => $order->id,
            'status' => $order->status,
            'total_price' => $order->total_price,
            'products' => $orderProducts
        ], 201);
    }

    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 10);

        $orders = Order::with(['products' => function($query) {
            $query->select('products.id', 'products.name', 'order_product.price', 'order_product.quantity', 'categories.name as category_name')
                  ->join('categories', 'products.category_id', '=', 'categories.id');
        }])->paginate($perPage);

        $orders->getCollection()->transform(function($order) {
            return [
                'id' => $order->id,
                'status' => $order->status,
                'total_price' => $order->total_price,
                'products' => $order->products->map(function($product) {
                    return [
                        'id' => $product->id,
                        'price' => $product->pivot->price,
                        'quantity' => $product->pivot->quantity,
                        'product' => [
                            'name' => $product->name
                        ],
                        'category' => [
                            'name' => $product->category_name 
                        ]
                    ];
                })
            ];
        });

        return response()->json([
            'dados' => $orders->items(),
            'pagina' => $orders->currentPage()
        ]);
    }

    public function show($id)
    {
        $order = Order::with(['products' => function($query) {
            $query->select('products.id', 'products.name', 'products.category_id', 'order_product.price', 'order_product.quantity')
                  ->join('categories', 'products.category_id', '=', 'categories.id')
                  ->addSelect('categories.name as category_name');
        }])->findOrFail($id);

        $formattedOrder = [
            'id' => $order->id,
            'status' => $order->status,
            'total_price' => $order->total_price,
            'products' => $order->products->map(function($product) {
                return [
                    'product_id' => $product->id,
                    'product' => [
                        'name' => $product->name
                    ],
                    'category' => [
                        'name' => $product->category_name  
                    ],
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
            ], 400);
        }

        $order = Order::with('products.category')->findOrFail($id);
        $order->update(['status' => $request->status]);

        $orderProducts = $order->products->map(function($product) {
            return [
                'product_id' => $product->id,
                'product' => [
                    'name' => $product->name
                ],
                'category' => [
                    'name' => $product->category->name
                ],
                'price' => $product->price,
                'quantity' => $product->pivot->quantity 
            ];
        });

        return response()->json([
            'id' => $order->id,
            'status' => $order->status,
            'total_price' => $order->total_price,
            'products' => $orderProducts
        ]);
    }
}
