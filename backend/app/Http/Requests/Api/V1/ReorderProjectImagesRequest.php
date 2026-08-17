<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class ReorderProjectImagesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'images' => [
                'required',
                'array',
                'min:1',
            ],

            'images.*.id' => [
                'required',
                'integer',
                'exists:project_images,id',
            ],

            'images.*.sort_order' => [
                'required',
                'integer',
                'min:0',
            ],
        ];
    }
}
