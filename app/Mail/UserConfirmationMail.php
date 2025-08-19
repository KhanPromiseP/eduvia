<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class UserConfirmationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $formData;
    public $servicesLink;

    public function __construct($formData)
    {
        $this->formData = $formData;
        $this->servicesLink = url('/service'); // Update with your actual services page URL
    }

    public function build()
    {
        return $this->from(config('mail.from.address'), config('mail.from.name'))
                   ->subject('Thank You for Contacting ' . config('app.name'))
                   ->view('emails.user.confirmation')
                   ->text('emails.user.confirmation_plain');
    }
}