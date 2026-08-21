<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class StoreProjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'title' => [
                'required',
                'string',
                'max:255',
            ],

            'slug' => [
                'nullable',
                'string',
                'max:255',
                'alpha_dash',
                'unique:projects,slug',
            ],

            'short_description' => [
                'nullable',
                'string',
                'max:1000',
            ],

            'description' => [
                'nullable',
                'string',
                'max:50000',
            ],

            'price' => [
                'nullable',
                'numeric',
                'min:0',
                'max:9999999999999.99',
            ],

            'currency' => [
                'required',
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
                'required',
                'in:draft,published,archived',
            ],

            'file' => [
                'nullable',
                'file',
                'mimes:zip',
                'max:512000',
            ],
        ];
    }
}
