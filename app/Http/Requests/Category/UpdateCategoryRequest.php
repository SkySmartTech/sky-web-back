<?php

namespace App\Http\Requests\Category;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('admin') ?? false;
    }

    public function rules(): array
    {
        $id = $this->route('category');

        return [
            'name' => ['sometimes','string','max:255'],
            'slug' => ['sometimes','string','max:255',"unique:categories,slug,{$id}"],
        ];
    }
}
