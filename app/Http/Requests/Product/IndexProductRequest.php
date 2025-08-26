<?php

namespace App\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;

class IndexProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; 
    }

    public function rules(): array
    {
        return [
            'search'      => ['nullable','string','max:255'],
            'category_id' => ['nullable','integer','exists:categories,id'],
            'category'    => ['nullable','string','max:255'], // slug
            'min_price'   => ['nullable','numeric','min:0'],
            'max_price'   => ['nullable','numeric','gte:min_price'],
            'sort'        => ['nullable','in:latest,price_asc,price_desc,title_asc,title_desc'],
            'per_page'    => ['nullable','integer','min:1','max:100'],
            'page'        => ['nullable','integer','min:1'],
        ];
    }
}
