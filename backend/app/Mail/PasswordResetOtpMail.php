<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PasswordResetOtpMail extends Mailable
{
    use Queueable;
    use SerializesModels;

    public function __construct(
        public readonly string $otp,
    ) {}

    public function build(): static
    {
        return $this
            ->subject('Password Reset OTP')
            ->view('emails.password-reset-otp');
    }
}
