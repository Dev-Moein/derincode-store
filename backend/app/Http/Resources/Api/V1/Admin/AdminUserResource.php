<?php

namespace App\Http\Resources\Api\V1\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AdminUserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [

            'id' => $this->id,

            'name' => $this->name,

            'email' => $this->email,

            'phone' => $this->phone,

            'roles' => $this->whenLoaded(
                'roles',
                function () {
                    return $this->roles->map(
                        function ($role) {
                            return [
                                'id' => $role->id,
                                'name' => $role->name,
                                'slug' => $role->slug,
                            ];
                        }
                    );
                }
            ),

            'permissions' => $this->whenLoaded(
                'roles',
                function () {
                    return $this->roles
                        ->flatMap(
                            fn ($role) =>
                                $role->permissions
                        )
                        ->unique('id')
                        ->values()
                        ->map(
                            function ($permission) {
                                return [
                                    'id' => $permission->id,
                                    'name' => $permission->name,
                                    'slug' => $permission->slug,
                                ];
                            }
                        );
                }
            ),

            'email_verified_at' =>
                $this->email_verified_at?->toISOString(),

            'created_at' =>
                $this->created_at?->toISOString(),

            'updated_at' =>
                $this->updated_at?->toISOString(),

        ];
    }
}
