<?php

namespace App\Http\Resources\Api\V1;

use App\Http\Resources\Api\V1\ProjectImageResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProjectResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,

            'title' => $this->title,

            'slug' => $this->slug,

            'short_description' => $this->short_description,

            'description' => $this->description,

            'price' => $this->price,

            'currency' => $this->currency,

            'is_for_sale' => (bool) $this->is_for_sale,

            'is_featured' => (bool) $this->is_featured,

            'status' => $this->status instanceof \BackedEnum
                ? $this->status->value
                : $this->status,

            'file' => [
                'name' => $this->file_name,
                'size' => $this->file_size,
            ],

            'published_at' => $this->published_at?->toISOString(),

            'images' => ProjectImageResource::collection(
                $this->whenLoaded('images')
            ),

            'created_at' => $this->created_at?->toISOString(),

            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
