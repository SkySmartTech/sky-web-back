<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('admin') ?? false;
    }

    public function rules(): array
    {
        $id = $this->route('user'); 

        return [
            'name'     => ['sometimes','string','max:255'],
            'email'    => ['sometimes','email','max:255',"unique:users,email,{$id}"],
            'role'     => ['sometimes','in:admin,editor,viewer'],
            'password' => ['sometimes','string','min:8','max:128'],
        ];
    }
}
