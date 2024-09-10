<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->input(), [
            'name' => 'required|string'    
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors()
            ], 422);
        }

        $category = Category::create($request->all());
        return response()->json($category, 201);
    }

    public function index()
    {
        return response()->json(Category::with('products')->paginate(10));
    }
}
