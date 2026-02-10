<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class VerifyTwoFactorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $code = $this->input('code');

        if (! is_string($code)) {
            return;
        }

        $normalized = str_replace([' ', '-'], '', trim($code));

        $this->merge([
            'code' => strtoupper($normalized),
        ]);
    }

    public function rules(): array
    {
        return [
            'code' => ['required', 'string', 'regex:/^(\\d{6}|[A-Z0-9]{10})$/'],
        ];
    }

    public function messages(): array
    {
        return [
            'code.required' => 'Enter your email verification or recovery code.',
            'code.regex' => 'Enter a valid 6-digit email code or a 10-character recovery code.',
        ];
    }
}
