<?php

namespace App\Jobs;

use App\Mail\PasswordResetOtpMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;

class SendPasswordResetOtpJob implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public readonly string $email,
        public readonly string $otp,
    ) {
    }

    public function handle(): void
    {
        Mail::to($this->email)
            ->send(new PasswordResetOtpMail($this->otp));
    }
}
