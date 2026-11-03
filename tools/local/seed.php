<?php

require __DIR__.'/bootstrap.php';

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

$accounts = [
    ['email' => 'cliente@propaps.test', 'name' => 'Cliente', 'last_name' => 'de Prueba', 'role' => 'client'],
    ['email' => 'asesor@propaps.test', 'name' => 'Asesor', 'last_name' => 'de Prueba', 'role' => 'advisor'],
    ['email' => 'administracion@propaps.test', 'name' => 'Administracion', 'last_name' => 'de Prueba', 'role' => 'admin'],
];
$credentialsFile = dirname(__DIR__, 2).'/.local/accesos-pruebas.txt';
$existing = User::whereIn('email', array_column($accounts, 'email'))->count();
if ($existing > 0) {
    echo "Ya existen cuentas de prueba. Se conservan sus claves y datos.\n";
    exit(0);
}
$password = 'Local-'.bin2hex(random_bytes(8));
DB::transaction(function () use ($accounts, $password): void {
    User::withoutEvents(function () use ($accounts, $password): void {
        foreach ($accounts as $account) {
            User::forceCreate($account + [
                'password' => Hash::make($password),
                'status' => 'active',
                'email_verified_at' => now(),
            ]);
        }
    });
});
$lines = [
    'ACCESOS EXCLUSIVOS DE PRUEBA LOCAL',
    'Direccion: http://127.0.0.1:8000/login',
    'Clave de las tres cuentas: '.$password,
    '',
];
foreach ($accounts as $account) {
    $lines[] = $account['role'].': '.$account['email'];
}
$lines[] = '';
$lines[] = 'Son cuentas ficticias de la base SQLite local. No se han creado en Hostinger.';
file_put_contents($credentialsFile, implode(PHP_EOL, $lines).PHP_EOL, LOCK_EX);
@chmod($credentialsFile, 0600);
echo "Tres cuentas ficticias creadas. Claves guardadas en .local/accesos-pruebas.txt.\n";
