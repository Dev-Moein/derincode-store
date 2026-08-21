<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PaymentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,

            'amount' => $this->amount,

            'currency' => $this->currency,

            'gateway' => $this->gateway,

            'transaction_id' => $this->transaction_id,

            'status' => $this->status instanceof \BackedEnum
                ? $this->status->value
                : $this->status,

            'paid_at' => $this->paid_at?->toISOString(),

            'project' => new ProjectResource(
                $this->whenLoaded('project')
            ),

            'created_at' => $this->created_at?->toISOString(),

            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
