<?php

namespace App\Http\Requests\Admin;

use App\Enums\Role;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserIndexRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<int, \Illuminate\Contracts\Validation\ValidationRule|string>>
     */
    public function rules(): array
    {
        return [
            'search' => ['nullable', 'string', 'max:255'],
            'role' => ['nullable', Rule::in([
                Role::Member->value,
                Role::Admin->value,
                Role::SuperAdmin->value,
            ])],
            'status' => ['nullable', Rule::in(['verified', 'unverified'])],
            'per_page' => ['nullable', 'integer', 'min:5', 'max:100'],
            'sort' => ['nullable', Rule::in(['name', 'email', 'created_at'])],
            'direction' => ['nullable', Rule::in(['asc', 'desc'])],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'role.in' => 'The selected role filter is invalid.',
            'status.in' => 'Status filter must be verified or unverified.',
            'per_page.min' => 'Per page must be at least 5.',
            'per_page.max' => 'Per page must not exceed 100.',
            'sort.in' => 'The selected sort field is invalid.',
            'direction.in' => 'Sort direction must be asc or desc.',
        ];
    }
}
