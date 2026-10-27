<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;

class AccountEmailVerification extends VerifyEmail
{
    protected function buildMailMessage($url)
    {
        $minutes = config('auth.verification.expire', 60);

        return (new MailMessage)
            ->subject('Confirma tu correo | Propaps.com')
            ->markdown('emails.account', [
                'heading' => 'Confirma tu correo electrónico',
                'intro' => 'Gracias por registrarte en Propaps.com. Confirma tu correo para completar este paso de tu cuenta.',
                'buttonLabel' => 'Confirmar mi correo',
                'accountActionUrl' => $url,
                'expiry' => "Este enlace vence en {$minutes} minutos.",
                'notice' => 'Si no creaste una cuenta en Propaps.com, puedes ignorar este mensaje.',
            ]);
    }
}
