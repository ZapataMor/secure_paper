<?php

declare(strict_types=1);
use Illuminate\Contracts\Console\Kernel;

if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

$root = dirname(__DIR__, 2);
if (! is_file($root.'/vendor/autoload.php')) {
    fwrite(STDERR, "Faltan las dependencias PHP del entorno local.\n");
    exit(1);
}
require $root.'/vendor/autoload.php';
$app = require $root.'/bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();

$normalize = static fn (string $path): string => strtolower(str_replace('\\', '/', $path));
$expectedDatabase = $normalize($root.'/.local/database.sqlite');
$checks = [
    'APP_ENV debe ser local' => $app->environment('local'),
    'APP_URL debe apuntar a este equipo' => config('app.url') === 'http://127.0.0.1:8000',
    'Se necesita una clave local' => filled(config('app.key')),
    'La base de datos debe ser SQLite' => config('database.default') === 'sqlite',
    'La base de datos debe estar dentro de .local' => $normalize((string) config('database.connections.sqlite.database')) === $expectedDatabase,
    'DB_URL debe estar vacio' => blank(config('database.connections.sqlite.url')),
    'El correo debe guardarse en archivos locales' => config('mail.default') === 'log',
    'Los avisos automaticos deben estar desactivados' => ! config('project_notifications.enabled'),
    'Wompi debe estar en sandbox y sin credenciales' => config('wompi.environment') === 'sandbox'
        && blank(config('wompi.public_key')) && blank(config('wompi.private_key'))
        && blank(config('wompi.integrity_secret')) && blank(config('wompi.events_secret')),
    'El acceso por pagos sandbox debe estar desactivado' => ! config('wompi.sandbox_access'),
    'Google debe estar sin credenciales reales' => blank(config('services.google.client_id')) && blank(config('services.google.client_secret')),
];
foreach ($checks as $message => $passed) {
    if (! $passed) {
        fwrite(STDERR, 'Entorno local detenido: '.$message.".\n");
        exit(1);
    }
}

return $app;
