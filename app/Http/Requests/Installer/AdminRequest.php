<?php

namespace App\Http\Requests\Installer;

use Illuminate\Foundation\Http\FormRequest;

class AdminRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'name'                  => ['required', 'string', 'min:2', 'max:100'],
            'email'                 => ['required', 'email', 'max:255'],
            'password'              => ['required', 'string', 'min:8', 'confirmed'],
            'password_confirmation' => ['required'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'     => 'Admin name is required.',
            'email.required'    => 'Admin email is required.',
            'email.email'       => 'Please enter a valid email address.',
            'password.required' => 'Password is required.',
            'password.min'      => 'Password must be at least 8 characters.',
            'password.confirmed'=> 'Passwords do not match.',
        ];
    }
}
