<?php

namespace App\Http\Requests\Gallery;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePhotoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('admin') ?? false;
    }

    public function rules(): array
    {
        return [
            'album_id'      => ['sometimes','nullable','exists:albums,id'],
            'title'         => ['sometimes','nullable','string','max:255'],
            'caption'       => ['sometimes','nullable','string'],
            'image'         => ['sometimes','image','mimes:jpg,jpeg,png,webp,avif','max:10240'],
            'display_order' => ['sometimes','integer','min:0'],
            'taken_at'      => ['sometimes','date','nullable'],
        ];
    }
}
