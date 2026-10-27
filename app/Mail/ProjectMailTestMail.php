<?php
namespace App\Mail;

use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class ProjectMailTestMail extends ProjectUpdateMail
{
    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Prueba de avisos | Propaps.com');
    }

    public function content(): Content
    {
        return new Content(view: 'emails.project-mail-test', text: 'emails.project-update-text');
    }
}
