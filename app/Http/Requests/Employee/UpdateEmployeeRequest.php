<?php

namespace App\Http\Requests\Employee;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEmployeeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('admin') ?? false; 
    }

    public function rules(): array
    {
        $id = $this->route('employee');

        return [
            'first_name' => ['sometimes','string','max:255'],
            'last_name'  => ['sometimes','string','max:255'],
            'job_title'  => ['sometimes','string','max:255'],
            'department' => ['sometimes','string','max:255'],
            'email'      => ['sometimes','nullable','email','max:255',"unique:employees,email,{$id}"],
            'phone'      => ['sometimes','nullable','string','max:50'],
            'photo'      => ['sometimes','image','mimes:jpg,jpeg,png,webp,avif','max:5120'],
        ];
    }
}
