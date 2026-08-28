<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DownloadResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,

            'downloaded_at' => $this->downloaded_at?->toISOString(),

            'ip_address' => $this->ip_address,

            'user_agent' => $this->user_agent,

            'project' => new ProjectResource(
                $this->whenLoaded('project')
            ),

            'payment' => new PaymentResource(
                $this->whenLoaded('payment')
            ),

            'created_at' => $this->created_at?->toISOString(),

            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
