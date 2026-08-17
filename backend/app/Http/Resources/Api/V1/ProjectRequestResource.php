<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProjectRequestResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,

            'user' => $this->whenLoaded('user', function () {
                return [
                    'id' => $this->user->id,
                    'name' => $this->user->name,
                    'email' => $this->user->email,
                ];
            }),

            'title' => $this->title,

            'description' => $this->description,

            'budget' => $this->budget,

            'currency' => $this->currency,

            'status' => $this->status instanceof \BackedEnum
                ? $this->status->value
                : $this->status,

            'admin_note' => $this->admin_note,

            'reviewed_at' => $this->reviewed_at?->toISOString(),

            'created_at' => $this->created_at?->toISOString(),

            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
