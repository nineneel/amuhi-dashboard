<?php

namespace App\Http\Requests\Admin;

use App\Enums\NewsStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class NewsUpdateRequest extends FormRequest
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
            'slug' => [
                'required',
                'string',
                'max:255',
                'alpha_dash',
                Rule::unique('news', 'slug')->ignore($this->route('news')),
            ],
            'title' => ['required', 'string', 'max:255'],
            'summary' => ['required', 'string'],
            'category' => ['required', 'string', 'max:255'],
            'tags' => ['nullable', 'array'],
            'tags.*' => ['string', 'max:100'],
            'badge' => ['nullable', 'string', 'max:255'],
            'cover_image' => ['nullable', 'string', 'max:2048'],
            'content' => ['required', 'array', 'min:1'],
            'content.*.type' => ['required', Rule::in(['paragraph', 'heading', 'list', 'quote'])],
            'content.*.text' => ['nullable', 'string'],
            'content.*.items' => ['nullable', 'array'],
            'content.*.items.*' => ['string'],
            'read_time_minutes' => ['nullable', 'integer', 'min:1'],
            'author_name' => ['required', 'string', 'max:255'],
            'related_slugs' => ['nullable', 'array'],
            'related_slugs.*' => ['string', 'max:255'],
            'status' => ['sometimes', Rule::enum(NewsStatus::class)],
            'published_at' => ['nullable', 'date'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'slug.required' => 'Slug is required.',
            'slug.alpha_dash' => 'Slug can only contain letters, numbers, dashes, and underscores.',
            'slug.unique' => 'Slug is already used by another article.',
            'title.required' => 'Title is required.',
            'summary.required' => 'Summary is required.',
            'category.required' => 'Category is required.',
            'content.required' => 'Content blocks are required.',
            'content.min' => 'At least one content block is required.',
            'author_name.required' => 'Author name is required.',
        ];
    }
}
