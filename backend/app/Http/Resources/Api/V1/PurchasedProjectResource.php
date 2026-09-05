<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PurchasedProjectResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->project->id,

            'title' => $this->project->title,

            'slug' => $this->project->slug,

            'short_description' =>
                $this->project->short_description,

            'price' => $this->project->price,

            'currency' => $this->project->currency,

            'file' => [
                'name' => $this->project->file_name,
                'size' => $this->project->file_size,
            ],

            'purchased_at' =>
                $this->paid_at?->toISOString(),

            'payment_id' => $this->id,

            'downloadable' =>
                ! empty($this->project->file_path),

            'images' => ProjectImageResource::collection(
                $this->project->images
            ),
        ];
    }
}
