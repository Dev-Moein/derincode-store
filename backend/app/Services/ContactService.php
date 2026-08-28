<?php

namespace App\Services;

use App\Contracts\Repositories\ContactRepositoryInterface;
use App\Contracts\Services\ContactServiceInterface;
use App\Models\Contact;

class ContactService implements ContactServiceInterface
{
    public function __construct(
        private readonly ContactRepositoryInterface $contactRepository,
    ) {}

    public function create(array $data): Contact
    {
        return $this->contactRepository->create($data);
    }
}

