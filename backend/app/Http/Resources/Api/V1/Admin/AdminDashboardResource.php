<?php

namespace App\Http\Resources\Api\V1\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AdminDashboardResource extends JsonResource
{
    public function toArray(
        Request $request
    ): array {
        return [
            'stats' => $this->resource['stats'],

            'recent_users' =>
                $this->resource['recent_users'],

            'recent_payments' =>
                $this->resource['recent_payments'],

            'recent_project_requests' =>
                $this->resource['recent_project_requests'],
        ];
    }
}
