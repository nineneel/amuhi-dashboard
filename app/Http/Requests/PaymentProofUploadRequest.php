<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PaymentProofUploadRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'plan_id' => [
                'required',
                'integer',
                Rule::exists('subscription_plans', 'id')
                    ->where('is_active', true)
                    ->where(fn ($query) => $query->where('duration_days', '>', 0)),
            ],
            'proof_image' => [
                'required',
                'image',
                'mimes:jpg,jpeg,png',
                'max:5120',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'plan_id.required' => 'Select a subscription plan to continue.',
            'plan_id.exists' => 'The selected plan is not available right now.',
            'proof_image.required' => 'Please upload your payment proof image.',
            'proof_image.image' => 'The file must be an image.',
            'proof_image.mimes' => 'The image must be a JPG, JPEG, or PNG file.',
            'proof_image.max' => 'The image must not exceed 5MB.',
        ];
    }
}
