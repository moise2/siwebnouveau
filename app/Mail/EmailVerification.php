<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class EmailVerification extends Mailable
{
    use Queueable, SerializesModels;

    public $verificationCode;
    public $email;

    public function __construct($verificationCode, $email)
    {
        $this->verificationCode = $verificationCode;
        $this->email = $email;
    }

    public function build()
    {
        return $this->view('frontend.pages.emails.verification_email')
            ->with([
                'code' => $this->verificationCode,
                'email' => $this->email,
                'verificationLink' => url('/verifySubscriber', [
                    'verification_code' => $this->verificationCode,
                    'email' => $this->email
                ]),
            ])
            ->subject("Code de vérification: {$this->verificationCode}");
    }
}
