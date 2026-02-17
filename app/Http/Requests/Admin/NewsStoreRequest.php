<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class NewsStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'unique:news,slug'],
            'summary' => ['required', 'string'],
            'category' => ['required', 'string', 'max:100'],
            'tags' => ['nullable', 'array'],
            'tags.*' => ['string', 'max:50'],
            'badge' => ['nullable', 'string', 'max:50'],
            'cover_image' => ['nullable', 'string', 'max:255'],
            'content' => ['required', 'array'],
            'read_time_minutes' => ['nullable', 'integer', 'min:1'],
            'author_name' => ['required', 'string', 'max:255'],
            'related_slugs' => ['nullable', 'array'],
            'related_slugs.*' => ['string'],
        ];
    }
}
