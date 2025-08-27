<?php

namespace App\Http\Controllers\Category;

use App\Http\Controllers\Controller;
use App\Http\Requests\Category\StoreCategoryRequest;
use App\Http\Requests\Category\UpdateCategoryRequest;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $q = Category::query();

        if ($s = $request->query('search')) {
            $q->where('name', 'like', "%{$s}%")
              ->orWhere('slug', 'like', "%{$s}%");
        }

        $perPage = (int)($request->query('per_page', 20));
        $perPage = max(1, min(100, $perPage));

        return response()->json(
            $q->orderBy('name')->paginate($perPage)->appends($request->query())
        );
    }

    public function show(Category $category)
    {
        return response()->json($category);
    }


    public function store(StoreCategoryRequest $request)
    {
        $data = $request->validated();


        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);

            $base = $data['slug']; $i = 2;
            while (Category::where('slug', $data['slug'])->exists()) {
                $data['slug'] = "{$base}-{$i}";
                $i++;
            }
        }

        $category = Category::create($data);

        return response()->json([
            'message' => 'Category created',
            'category' => $category,
        ], 201);
    }

    public function update(UpdateCategoryRequest $request, Category $category)
    {
        $data = $request->validated();

        $category->update($data);

        return response()->json([
            'message' => 'Category updated',
            'category' => $category,
        ]);
    }

    public function destroy(Category $category)
    {
        if ($category->products()->exists()) {
            return response()->json([
                'message' => 'Cannot delete: category has products',
            ], 409);
        }

        $category->delete();

        return response()->json(['message' => 'Category deleted']);
    }
}
