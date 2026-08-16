<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $project = $this->route('project');

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
                    ->ignore($project),
            ],

            'short_description' => [
                'sometimes',
                'nullable',
                'string',
                'max:255',
            ],

            'description' => [
                'sometimes',
                'nullable',
                'string',
            ],

            'price' => [
                'sometimes',
                'nullable',
                'numeric',
                'min:0',
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

            'file_path' => [
                'sometimes',
                'nullable',
                'string',
                'max:255',
            ],

            'file_name' => [
                'sometimes',
                'nullable',
                'string',
                'max:255',
            ],

            'file_size' => [
                'sometimes',
                'nullable',
                'integer',
                'min:0',
            ],

            'published_at' => [
                'sometimes',
                'nullable',
                'date',
            ],
        ];
    }
}
