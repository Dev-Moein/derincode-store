<?php

namespace App\Contracts\Services;

use App\Models\Contact;

interface ContactServiceInterface
{
    public function create(array $data): Contact;
}

