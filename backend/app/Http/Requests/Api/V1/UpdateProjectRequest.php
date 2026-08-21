<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        $slug = $this->route('slug');

        return [
            'title' => [
                'sometimes',
                'string',
                'max:255',
            ],

            'slug' => [
                'sometimes',
                'string',
                'max:255',
                'alpha_dash',
                Rule::unique('projects', 'slug')
                    ->ignore($slug, 'slug'),
            ],

            'short_description' => [
                'sometimes',
                'nullable',
                'string',
                'max:1000',
            ],

            'description' => [
                'sometimes',
                'nullable',
                'string',
                'max:50000',
            ],

            'price' => [
                'sometimes',
                'nullable',
                'numeric',
                'min:0',
                'max:9999999999999.99',
            ],

            'currency' => [
                'sometimes',
                'string',
                'size:3',
                'uppercase',
            ],

            'is_for_sale' => [
                'sometimes',
                'boolean',
            ],

            'is_featured' => [
                'sometimes',
                'boolean',
            ],

            'status' => [
                'sometimes',
                Rule::in([
                    'draft',
                    'published',
                    'archived',
                ]),
            ],

            'file' => [
                'sometimes',
                'nullable',
                'file',
                'mimes:zip',
                'max:512000',
            ],
        ];
    }
}
