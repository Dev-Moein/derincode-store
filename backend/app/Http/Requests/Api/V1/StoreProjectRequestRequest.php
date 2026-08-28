<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class StoreProjectRequestRequest extends FormRequest
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

            'description' => [
                'required',
                'string',
                'max:10000',
            ],

            'budget' => [
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

        ];
    }
}
