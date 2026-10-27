<?php

use App\Http\Controllers\Admin\PaymentBillingController;
use App\Http\Controllers\Admin\PaymentQuoteController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\Admin\WorkController;
use App\Http\Controllers\Auth\GoogleAuthController;
use App\Http\Controllers\DocumentFileController;
use App\Http\Controllers\PrivateDocumentUploadController;
use App\Http\Controllers\WompiPaymentController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');
Route::view('/servicios', 'servicios')->name('services');
Route::view('/nosotros', 'nosotros')->name('about');
Route::view('/planes', 'planes')->name('plans');
Route::view('/terminos', 'legal', ['legalDocument' => 'terms', 'legalTitle' => 'Términos y condiciones'])->name('legal.terms');
Route::view('/privacidad', 'legal', ['legalDocument' => 'privacy', 'legalTitle' => 'Política de privacidad'])->name('legal.privacy');
Route::view('/preguntas-frecuentes', 'legal', ['legalDocument' => 'faq', 'legalTitle' => 'Preguntas frecuentes'])->name('legal.faq');

Route::middleware(['auth', 'verified'])->group(function () {

    Route::view('dashboard', 'dashboard')->name('dashboard');

    Route::get('dashboard/documentos/{document}/archivo', [DocumentFileController::class, 'show'])
        ->name('documents.file.show');

    Route::middleware('role:client')->group(function () {

        Route::get('dashboard/planes', [WompiPaymentController::class, 'index'])->name('private.planes');
        Route::post('dashboard/pagos', [WompiPaymentController::class, 'store'])->middleware('throttle:10,1')->name('private.payments.store');
        Route::get('dashboard/pagos/{order}', [WompiPaymentController::class, 'show'])->middleware('throttle:30,1')->name('private.payments.show');

        Route::middleware('membership')->group(function () {
            Route::get('dashboard/cargar-documento', [PrivateDocumentUploadController::class, 'index'])->name('private.upload-document');
            Route::post('dashboard/cargar-documento', [PrivateDocumentUploadController::class, 'store'])->name('private.upload-document.store');
        });
    });

    Route::middleware('role:admin')
        ->prefix('admin')
        ->name('admin.')
        ->group(function () {

            Route::get('correo/prueba-avisos', [\App\Http\Controllers\Admin\ProjectMailTestController::class, 'index'])->name('mail-test');
            Route::post('correo/prueba-avisos', [\App\Http\Controllers\Admin\ProjectMailTestController::class, 'store'])->middleware('throttle:3,1')->name('mail-test.store');
            Route::get('correo/diagnostico', \App\Http\Controllers\Admin\MailDiagnosticController::class)->middleware('throttle:10,1')->name('mail.diagnostic');
            Route::get('users', [UserManagementController::class, 'index'])->name('users.index');
            Route::post('trabajos/{user}/asignacion', [\App\Http\Controllers\Admin\ProjectAssignmentController::class, 'store'])->middleware('throttle:20,1')->name('works.assignment');
            Route::post('pagos/datos-fiscales', [PaymentBillingController::class, 'store'])->middleware('throttle:20,1')->name('payment-billing.store');
            Route::get('cotizaciones', [PaymentQuoteController::class, 'index'])->name('payment-quotes');
            Route::post('cotizaciones/acceso', [PaymentQuoteController::class, 'access'])->middleware('throttle:20,1')->name('service-access.store');
            Route::post('cotizaciones', [PaymentQuoteController::class, 'store'])->middleware('throttle:20,1')->name('payment-quotes.store');
        });

    Route::middleware('role:admin,advisor')
        ->prefix('admin')
        ->name('admin.')
        ->group(function () {
            Route::get('trabajos', [WorkController::class, 'index'])->name('works.index');
            Route::post('trabajos/{user}/estado', [\App\Http\Controllers\Admin\ProjectAssignmentController::class, 'progress'])->middleware('throttle:30,1')->name('works.progress');
            Route::get('trabajos/{user}', [WorkController::class, 'show'])->name('works.show');
            Route::post('trabajos/{user}/enviar', [WorkController::class, 'store'])->name('works.store');
        });
});

Route::get('/auth/google', [GoogleAuthController::class, 'redirect'])
    ->middleware('guest')
    ->name('google.redirect');

Route::get('/auth/google/callback', [GoogleAuthController::class, 'callback'])
    ->middleware('guest')
    ->name('google.callback');

Route::get('/auth/google/confirm', [GoogleAuthController::class, 'confirm'])->middleware('guest')->name('google.confirm');
Route::post('/auth/google/confirm', [GoogleAuthController::class, 'register'])->middleware(['guest', 'throttle:10,1'])->name('google.register');

require __DIR__.'/settings.php';

Route::post('pagos/wompi/eventos/{environment}', [WompiPaymentController::class, 'webhook'])->whereIn('environment', ['sandbox', 'production'])->middleware('throttle:120,1')->name('wompi.webhook');
