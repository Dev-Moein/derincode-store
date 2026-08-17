<?php

namespace App\Http\Requests\Api\V1;

use App\Enums\ProjectRequestStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProjectRequestStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'status' => [
                'required',
                Rule::enum(ProjectRequestStatus::class),
            ],

            'admin_note' => [
                'nullable',
                'string',
                'max:10000',
            ],
        ];
    }
}
