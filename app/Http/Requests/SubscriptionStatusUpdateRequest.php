<?php

namespace App\Http\Requests;

use App\SubscriptionStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SubscriptionStatusUpdateRequest extends FormRequest
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
                Rule::exists('subscription_plans', 'id')->where('is_active', true),
            ],
            'status' => ['required', Rule::enum(SubscriptionStatus::class)],
        ];
    }

    public function messages(): array
    {
        return [
            'plan_id.required' => 'Choose a plan for the demo status update.',
            'plan_id.exists' => 'The selected plan is not available right now.',
            'status.required' => 'Choose a subscription status to continue.',
        ];
    }
}
