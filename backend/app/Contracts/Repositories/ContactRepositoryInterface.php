<?php

namespace App\Contracts\Repositories;

use App\Models\Contact;

interface ContactRepositoryInterface
{
    public function create(array $data): Contact;
}

