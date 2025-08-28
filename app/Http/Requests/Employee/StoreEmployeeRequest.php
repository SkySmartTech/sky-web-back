<?php

namespace App\Http\Requests\Employee;

use Illuminate\Foundation\Http\FormRequest;

class StoreEmployeeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('admin') ?? false; 
    }

    public function rules(): array
    {
        return [
            'first_name' => ['required','string','max:255'],
            'last_name'  => ['required','string','max:255'],
            'job_title'  => ['nullable','string','max:255'],
            'department' => ['nullable','string','max:255'],
            'email'      => ['nullable','email','max:255','unique:employees,email'],
            'phone'      => ['nullable','string','max:50'],
            'photo'      => ['nullable','image','mimes:jpg,jpeg,png,webp,avif','max:5120'],
        ];
    }
}
