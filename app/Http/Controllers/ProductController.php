<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'price' => 'required|numeric',
            'category_id' => 'required|exists:categories,id'
        ]);

        $product = Product::create($request->all());
        return response()->json($product, 201);
    }

    public function index()
    {
        return response()->json(Product::paginate(10));
    }
}
