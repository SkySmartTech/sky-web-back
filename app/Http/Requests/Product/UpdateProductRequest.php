<?php

namespace App\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('admin') ?? false;
    }

    public function rules(): array
    {
        $id = $this->route('product');

        return [
            'category_id' => ['sometimes','exists:categories,id'],
            'title'       => ['sometimes','string','max:255'],
            'slug'        => ['sometimes','string','max:255',"unique:products,slug,{$id}"],
            'description' => ['sometimes','string'],
            'price'       => ['sometimes','numeric','min:0'],
            'image'       => ['sometimes','image','mimes:jpg,jpeg,png,webp,avif','max:5120'],
            'thumbnail'   => ['sometimes','image','mimes:jpg,jpeg,png,webp,avif','max:5120'],
            'url'         => ['sometimes','url'],
        ];
    }
}
