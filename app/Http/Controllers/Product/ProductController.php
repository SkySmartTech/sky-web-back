<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\Controller;
use App\Http\Requests\Product\StoreProductRequest;
use App\Http\Requests\Product\UpdateProductRequest;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Requests\Product\IndexProductRequest;
use App\Models\Category;

class ProductController extends Controller
{

    public function index(IndexProductRequest $request)
    {
        $data = $request->validated();

        $q = Product::query()->with('category');

        if (!empty($data['search'])) {
            $term = $data['search'];
            $q->where(function ($qq) use ($term) {
                $qq->where('title', 'like', "%{$term}%")
                ->orWhere('description', 'like', "%{$term}%");
            });
        }


        if (!empty($data['category_id'])) {
            $q->where('category_id', $data['category_id']);
        }


        if (!empty($data['category'])) {
            $slug = $data['category'];
            $q->whereHas('category', fn ($c) => $c->where('slug', $slug));
        }


        if (isset($data['min_price'])) {
            $q->where('price', '>=', $data['min_price']);
        }
        if (isset($data['max_price'])) {
            $q->where('price', '<=', $data['max_price']);
        }


        switch ($data['sort'] ?? 'latest') {
            case 'price_asc':  $q->orderBy('price', 'asc'); break;
            case 'price_desc': $q->orderBy('price', 'desc'); break;
            case 'title_asc':  $q->orderBy('title', 'asc'); break;
            case 'title_desc': $q->orderBy('title', 'desc'); break;
            default:           $q->latest(); break; // latest
        }

        $perPage = $data['per_page'] ?? 10;

        return response()->json(
            $q->paginate($perPage)->appends($data)
        );
    }
    public function show(Product $product)
    {
        return response()->json($product->load('category'));
    }

    public function store(StoreProductRequest $request)
    {
        $product = Product::create($request->validated());
        return response()->json(['message' => 'Product created', 'product' => $product], 201);
    }

    public function update(UpdateProductRequest $request, Product $product)
    {
        $product->update($request->validated());
        return response()->json(['message' => 'Product updated', 'product' => $product]);
    }

    public function destroy(Product $product)
    {
        $product->delete();
        return response()->json(['message' => 'Product deleted']);
    }


}
