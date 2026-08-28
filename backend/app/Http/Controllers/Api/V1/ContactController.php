<?php

namespace App\Http\Controllers\Api\V1;

use App\Contracts\Services\ContactServiceInterface;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StoreContactRequest;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;

class ContactController extends Controller
{
    public function __construct(
        private readonly ContactServiceInterface $contactService,
    ) {}

    /**
     * Store a new contact message.
     */
    public function store(
        StoreContactRequest $request
    ): JsonResponse {
        $contact = $this->contactService->create(
            $request->validated()
        );

        return ApiResponse::success(
            data: [
                'contact' => $contact,
            ],
            message: 'Your message has been sent successfully.',
            status: 201,
        );
    }
}

