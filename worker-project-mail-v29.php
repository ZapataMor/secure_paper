<?php

// Utilidad privada: instalar junto a artisan, fuera de public_html.
if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

$lock = @fopen(__DIR__.'/storage/framework/project-mail-worker.lock', 'c');
if (!$lock || !flock($lock, LOCK_EX | LOCK_NB)) {
    if (is_resource($lock)) fclose($lock);
    exit(0);
}

$state = ['worker_version' => 29, 'checked_at_utc' => gmdate('c'), 'status' => 'starting', 'mail_sent_by_probe' => false];
$code = 0;
try {
    require __DIR__.'/vendor/autoload.php';
    $app = require __DIR__.'/bootstrap/app.php';
    $app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
    $state['notifications_enabled'] = (bool) config('project_notifications.enabled');
    $state['smtp_selected'] = config('mail.default') === 'smtp';
    $state['database_queue_configured'] = config('queue.connections.database.driver') === 'database';
    $schema = Illuminate\Support\Facades\Schema::connection(config('queue.connections.database.connection'));
    $table = config('queue.connections.database.table', 'jobs');
    $state['jobs_schema_ready'] = $schema->hasColumns($table, ['id', 'queue', 'payload', 'attempts', 'reserved_at', 'available_at', 'created_at']);
    $failedSchema = Illuminate\Support\Facades\Schema::connection(config('queue.failed.database'));
    $state['failed_jobs_schema_ready'] = $failedSchema->hasColumns(config('queue.failed.table', 'failed_jobs'), ['id', 'uuid', 'connection', 'queue', 'payload', 'exception', 'failed_at']);
    $state['admin_recipient_ready'] = Illuminate\Support\Facades\Schema::hasTable('users')
        && App\Models\User::where('role', 'admin')->where('status', 'active')->whereNotNull('email_verified_at')
            ->whereIn('email', config('project_notifications.admin_recipients', []))->exists();
    if (!$state['notifications_enabled']) {
        $state['status'] = 'disabled';
    } elseif (!$state['smtp_selected'] || !$state['database_queue_configured'] || !$state['jobs_schema_ready'] || !$state['failed_jobs_schema_ready']) {
        $state['status'] = 'not_ready';
        $code = 1;
    } else {
        $code = Illuminate\Support\Facades\Artisan::call('queue:work', [
            'connection' => 'database', '--queue' => 'project-mail', '--stop-when-empty' => true,
            '--tries' => 3, '--timeout' => 30, '--max-time' => 50, '--sleep' => 1,
        ]);
        $state['status'] = $code === 0 ? 'completed' : 'failed';
    }
} catch (Throwable $exception) {
    $state['status'] = 'failed';
    $state['exception_class'] = $exception::class;
    $code = 1;
} finally {
    // Informe acotado: sin credenciales, direcciones, datos de cola ni excepciones completas.
    $directory = __DIR__.'/storage/app/private';
    if (is_dir($directory)) {
        $temporary = tempnam($directory, 'project-mail-state-');
        if ($temporary !== false) {
            file_put_contents($temporary, json_encode($state, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)."\n");
            chmod($temporary, 0600);
            rename($temporary, $directory.'/project-mail-worker-v29.json');
        }
    }
    flock($lock, LOCK_UN);
    fclose($lock);
}
exit($code);
