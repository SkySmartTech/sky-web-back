<?php

namespace App\Http\Requests\Gallery;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAlbumRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('admin') ?? false;
    }

    public function rules(): array
    {
        $id = $this->route('album');

        return [
            'title'       => ['sometimes','string','max:255'],
            'slug'        => ['sometimes','string','max:255',"unique:albums,slug,{$id}"],
            'description' => ['sometimes','string'],
            'cover_image' => ['sometimes','image','mimes:jpg,jpeg,png,webp,avif','max:5120'],
        ];
    }
}
