<?php

namespace App\Http\Requests\Auth;

use App\MemberType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', 'min:8'],
            'member_type' => ['required', Rule::enum(MemberType::class)],
            'phone' => ['required', 'string', 'max:25'],
            'company_name' => ['nullable', 'string', 'max:255'],
            'terms' => ['accepted'],
        ];
    }
}
