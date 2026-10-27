<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Notifications\Messages\MailMessage;

class AccountPasswordReset extends ResetPassword
{
    protected function buildMailMessage($url)
    {
        $minutes = config('auth.passwords.'.config('auth.defaults.passwords').'.expire');

        return (new MailMessage)
            ->subject('Recupera tu contraseña | Propaps.com')
            ->markdown('emails.account', [
                'heading' => 'Recupera tu contraseña',
                'intro' => 'Recibimos una solicitud para recuperar el acceso a tu cuenta en Propaps.com.',
                'buttonLabel' => 'Crear una nueva contraseña',
                'accountActionUrl' => $url,
                'expiry' => "Este enlace vence en {$minutes} minutos y solo puede utilizarse una vez.",
                'notice' => 'Si no realizaste esta solicitud, puedes ignorar este mensaje. Tu contraseña seguirá siendo la misma.',
            ]);
    }
}
