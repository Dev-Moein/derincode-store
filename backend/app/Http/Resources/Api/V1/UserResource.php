<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'email_verified_at' => $this->email_verified_at,
            'phone_verified_at' => $this->phone_verified_at,

            'roles' => $this->whenLoaded(
                'roles',
                fn () => $this->roles->pluck('slug')
            ),

            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
