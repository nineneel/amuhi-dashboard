<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PaymentSimulationRequest extends FormRequest
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
        ];
    }

    public function messages(): array
    {
        return [
            'plan_id.required' => 'Select a subscription plan to continue.',
            'plan_id.exists' => 'The selected plan is not available right now.',
        ];
    }
}
