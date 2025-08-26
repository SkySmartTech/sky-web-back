<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\Controller;
use App\Http\Requests\Product\StoreProductRequest;
use App\Http\Requests\Product\UpdateProductRequest;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    // Public list
    public function index(Request $request)
    {
        $products = Product::with('category')->latest()->paginate(10);
        return response()->json($products);
    }

    // Public single
    public function show(Product $product)
    {
        return response()->json($product->load('category'));
    }

    // Admin create
    public function store(StoreProductRequest $request)
    {
        $product = Product::create($request->validated());
        return response()->json(['message' => 'Product created', 'product' => $product], 201);
    }

    // Admin update
    public function update(UpdateProductRequest $request, Product $product)
    {
        $product->update($request->validated());
        return response()->json(['message' => 'Product updated', 'product' => $product]);
    }

    // Admin delete
    public function destroy(Product $product)
    {
        $product->delete();
        return response()->json(['message' => 'Product deleted']);
    }
}
