<?php

namespace App\Notifications\Otp;

use App\Contracts\Notifications\OtpNotificationInterface;
use App\Jobs\SendPasswordResetOtpJob;

class EmailOtpNotification implements OtpNotificationInterface
{
    public function send(
        string $recipient,
        string $otp
    ): void {
        SendPasswordResetOtpJob::dispatch(
            $recipient,
            $otp
        );
    }
}
