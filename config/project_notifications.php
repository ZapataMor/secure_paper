<?php

return [
    // Activar solo después de comprobar SMTP y el trabajador de esta cola.
    'enabled' => (bool) env('PROPAPS_PROJECT_EMAIL_ENABLED', false),
    'admin_recipients' => array_values(array_filter(array_map(
        fn ($email) => strtolower(trim($email)),
        explode(',', (string) env('PROPAPS_PROJECT_ADMIN_EMAILS', 'gerencia@propaps.com'))
    ), fn ($email) => filter_var($email, FILTER_VALIDATE_EMAIL))),
];
