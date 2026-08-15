<?php

namespace App\Contracts\Notifications;

interface OtpNotificationInterface
{
    public function send(
        string $recipient,
        string $otp
    ): void;
}
