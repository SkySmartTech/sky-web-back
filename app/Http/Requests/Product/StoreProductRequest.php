<?php

namespace App\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('admin') ?? false;
    }

    public function rules(): array
    {

        return [
            'category_id' => ['required','exists:categories,id'],
            'title'       => ['required','string','max:255'],
            'slug'        => ['required','string','max:255','unique:products,slug'],
            'description' => ['nullable','string'],
            'price'       => ['nullable','numeric','min:0'],
            'image'       => ['nullable','image','mimes:jpg,jpeg,png,webp,avif','max:5120'],
            'thumbnail'   => ['nullable','image','mimes:jpg,jpeg,png,webp,avif','max:5120'],
            'url'         => ['nullable','url'],
        ];
    }
}
