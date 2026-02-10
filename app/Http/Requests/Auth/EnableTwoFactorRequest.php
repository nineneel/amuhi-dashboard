<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class EnableTwoFactorRequest extends FormRequest
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

        $this->merge([
            'code' => str_replace([' ', '-'], '', trim($code)),
        ]);
    }

    public function rules(): array
    {
        return [
            'code' => ['required', 'string', 'digits:6'],
        ];
    }

    public function messages(): array
    {
        return [
            'code.required' => 'Enter the 6-digit code we emailed you.',
            'code.digits' => 'The authentication code must be exactly 6 digits.',
        ];
    }
}
