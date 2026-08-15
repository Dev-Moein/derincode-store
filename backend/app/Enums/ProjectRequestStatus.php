<?php

namespace App\Enums;

enum ProjectRequestStatus: string
{
    case PENDING = 'pending';
    case REVIEWING = 'reviewing';
    case ACCEPTED = 'accepted';
    case REJECTED = 'rejected';
    case COMPLETED = 'completed';
    case CANCELLED = 'cancelled';
}
