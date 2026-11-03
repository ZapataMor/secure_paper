<?php

require __DIR__.'/bootstrap.php';

use App\Models\PaymentPlan;
use App\Models\User;
use App\Models\UserSubscription;
use Illuminate\Support\Facades\DB;

$user = User::where('email', 'cliente@propaps.test')->where('role', 'client')->where('status', 'active')->firstOrFail();
$planName = 'PRUEBA LOCAL - Revision de manuscrito - Sin cobro';
$existing = $user->activePaidSubscription()->with('paymentPlan')->first();
if ($existing) {
    if ($existing->paymentPlan?->name !== $planName) {
        throw new RuntimeException('La cuenta ya tiene otro servicio activo. Se conserva sin cambios.');
    }
    echo 'Servicio ficticio ya activo hasta '.$existing->end_date->format('d/m/Y').". Se conserva sin duplicarlo.\n";
    exit(0);
}
if (UserSubscription::where('user_id', $user->id)->exists()) {
    throw new RuntimeException('La cuenta tiene un servicio previo. Revisarlo antes de crear otro.');
}

$backupDirectory = dirname(__DIR__, 2).'/.local/backups';
if (! is_dir($backupDirectory) && ! mkdir($backupDirectory, 0700, true)) {
    throw new RuntimeException('No se pudo crear la carpeta de respaldos.');
}
$backup = $backupDirectory.'/antes-servicio-ficticio-'.now()->format('Ymd-His').'-'.bin2hex(random_bytes(3)).'.sqlite';
$pdo = DB::connection()->getPdo();
$pdo->exec('VACUUM INTO '.$pdo->quote($backup));
$checkBackup = new PDO('sqlite:'.$backup);
if ($checkBackup->query('PRAGMA integrity_check')->fetchColumn() !== 'ok') {
    throw new RuntimeException('El respaldo no paso la verificacion. No se activa el servicio.');
}
$checkBackup = null;

$subscription = DB::transaction(function () use ($user, $planName) {
    $plan = PaymentPlan::firstOrCreate(['name' => $planName], [
        'description' => 'Servicio ficticio exclusivo de SQLite local. Estado de pago simulado; sin orden Wompi ni cobro. Permite revisar la carga de documentos.',
        'price' => 0,
        'duration_days' => 30,
        'max_documents' => 10,
        'includes_meetings' => false,
        'meetings_limit' => 0,
        'status' => 'active',
    ]);

    return UserSubscription::create([
        'user_id' => $user->id,
        'payment_plan_id' => $plan->id,
        'start_date' => today(),
        'end_date' => today()->addDays(29),
        'status' => 'active',
        'payment_status' => 'paid', // Estado ficticio requerido por el acceso, exclusivamente local.
        'amount_paid' => 0,
    ]);
});

echo 'Respaldo verificado: '.$backup.PHP_EOL;
echo 'Servicio: '.$planName.PHP_EOL;
echo 'Vigencia: '.$subscription->start_date->format('d/m/Y').' - '.$subscription->end_date->format('d/m/Y').PHP_EOL;
echo "Importe: 0. No se crearon ordenes ni transacciones de pago.\n";
