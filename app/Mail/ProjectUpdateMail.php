<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class ProjectUpdateMail extends Mailable
{
    public function __construct(public readonly string $notice, public readonly string $workspaceUrl) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Hay novedades en tu proyecto | Propaps.com');
    }

    public function content(): Content
    {
        return new Content(view: 'emails.project-update', text: 'emails.project-update-text');
    }
}
