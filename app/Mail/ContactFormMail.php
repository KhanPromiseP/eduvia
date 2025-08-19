<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ContactFormMail extends Mailable
{
    use Queueable, SerializesModels;

    public $formData;

    public function __construct($formData)
    {
        $this->formData = $formData;
    }

    public function build()
    {
        return $this->from(config('mail.from.address'), config('mail.from.name'))
                   ->subject('New Contact Form Submission: ' . ($this->formData['service'] ?? 'General Inquiry'))
                   ->view('emails.admin.contact')
                   ->text('emails.admin.contact_plain');
    }
}