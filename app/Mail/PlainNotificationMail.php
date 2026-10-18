<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class PlainNotificationMail extends Mailable
{
    public function __construct(public readonly string $subjectLine, public readonly string $text) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: $this->subjectLine);
    }

    public function content(): Content
    {
        return new Content(view: 'emails.plain', text: 'emails.plain-text');
    }
}
