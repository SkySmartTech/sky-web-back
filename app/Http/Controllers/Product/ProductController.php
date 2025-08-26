<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\Controller;
use App\Http\Requests\Product\IndexProductRequest;
use App\Http\Requests\Product\StoreProductRequest;
use App\Http\Requests\Product\UpdateProductRequest;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

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
            default:           $q->latest(); break;
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
        $data = $request->validated();


        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('products', 'public');
            $data['image'] = 'storage/' . $path;
        }
        if ($request->hasFile('thumbnail')) {
            $thumbPath = $request->file('thumbnail')->store('products', 'public');
            $data['thumbnail'] = 'storage/' . $thumbPath;
        }

        $product = Product::create($data);

        return response()->json([
            'message' => 'Product created',
            'product' => $product->load('category'),
        ], 201);
    }


    public function update(UpdateProductRequest $request, Product $product)
    {
        $data = $request->validated();


        $oldImage = $product->image;
        $oldThumb = $product->thumbnail;


        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('products', 'public');
            $data['image'] = 'storage/' . $path;


            if ($oldImage && str_starts_with($oldImage, 'storage/')) {
                Storage::disk('public')->delete(str_replace('storage/', '', $oldImage));
            }
        }

        if ($request->hasFile('thumbnail')) {
            $thumbPath = $request->file('thumbnail')->store('products', 'public');
            $data['thumbnail'] = 'storage/' . $thumbPath;

            if ($oldThumb && str_starts_with($oldThumb, 'storage/')) {
                Storage::disk('public')->delete(str_replace('storage/', '', $oldThumb));
            }
        }

        $product->update($data);

        return response()->json([
            'message' => 'Product updated',
            'product' => $product->load('category'),
        ]);
    }

    public function destroy(Product $product)
    {
        if ($product->image && str_starts_with($product->image, 'storage/')) {
            Storage::disk('public')->delete(str_replace('storage/', '', $product->image));
        }
        if ($product->thumbnail && str_starts_with($product->thumbnail, 'storage/')) {
            Storage::disk('public')->delete(str_replace('storage/', '', $product->thumbnail));
        }

        $product->delete();

        return response()->json(['message' => 'Product deleted']);
    }
}
