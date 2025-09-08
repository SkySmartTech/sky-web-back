<?php

namespace App\Http\Requests\Gallery;

use Illuminate\Foundation\Http\FormRequest;

class StorePhotoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('admin') ?? false;
    }

    public function rules(): array
    {
        return [
            'album_id'      => ['nullable','exists:albums,id'],
            'title'         => ['nullable','string','max:255'],
            'caption'       => ['nullable','string'],
            'image'         => ['required','image','mimes:jpg,jpeg,png,webp,avif','max:10240'], // 10MB
            'display_order' => ['nullable','integer','min:0'],
            'taken_at'      => ['nullable','date'],
        ];
    }
}
