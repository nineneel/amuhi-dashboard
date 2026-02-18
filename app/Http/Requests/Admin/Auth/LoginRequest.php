<?php

namespace App\Http\Requests\Admin\Auth;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
            'remember' => ['sometimes', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'email.required' => __('ui.admin.email_is_required'),
            'email.email' => __('ui.admin.please_enter_a_valid_email_address'),
            'password.required' => __('ui.admin.password_is_required'),
        ];
    }
}
