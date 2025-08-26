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
            'image'       => ['nullable','string','max:255'],
            'thumbnail'   => ['nullable','string','max:255'],
            'url'         => ['nullable','url'],
        ];
    }
}
