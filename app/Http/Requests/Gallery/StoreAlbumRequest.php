<?php

namespace App\Http\Requests\Gallery;

use Illuminate\Foundation\Http\FormRequest;

class StoreAlbumRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('admin') ?? false;
    }

    public function rules(): array
    {
        return [
            'title'       => ['required','string','max:255'],
            'slug'        => ['nullable','string','max:255','unique:albums,slug'],
            'description' => ['nullable','string'],
            'cover_image' => ['nullable','image','mimes:jpg,jpeg,png,webp,avif','max:5120'],
        ];
    }
}
