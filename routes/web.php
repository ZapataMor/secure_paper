<?php

use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\Admin\WorkController;
use App\Http\Controllers\DocumentFileController;
use App\Http\Controllers\PrivateDocumentUploadController;
use App\Http\Controllers\PrivatePlanController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\GoogleAuthController;

Route::view('/', 'welcome')->name('home');
Route::view('/servicios', 'servicios')->name('services');
Route::view('/nosotros', 'nosotros')->name('about');
Route::view('/planes', 'planes')->name('plans');

Route::middleware(['auth', 'verified'])->group(function () {

    Route::view('dashboard', 'dashboard')->name('dashboard');

    Route::get('dashboard/documentos/{document}/archivo', [DocumentFileController::class, 'show'])
        ->name('documents.file.show');

    Route::middleware('role:client')->group(function () {

        Route::get('dashboard/planes', [PrivatePlanController::class, 'index'])->name('private.planes');
        Route::post('dashboard/planes/{paymentPlan}/solicitar', [PrivatePlanController::class, 'select'])->name('private.planes.select');

        Route::middleware('membership')->group(function () {
            Route::get('dashboard/cargar-documento', [PrivateDocumentUploadController::class, 'index'])->name('private.upload-document');
            Route::post('dashboard/cargar-documento', [PrivateDocumentUploadController::class, 'store'])->name('private.upload-document.store');
        });
    });

    Route::middleware('role:admin')
        ->prefix('admin')
        ->name('admin.')
        ->group(function () {

            Route::get('users', [UserManagementController::class, 'index'])->name('users.index');
        });

    Route::middleware('role:admin,advisor')
        ->prefix('admin')
        ->name('admin.')
        ->group(function () {
            Route::get('trabajos', [WorkController::class, 'index'])->name('works.index');
            Route::get('trabajos/{user}', [WorkController::class, 'show'])->name('works.show');
            Route::post('trabajos/{user}/enviar', [WorkController::class, 'store'])->name('works.store');
        });
});

    Route::get('/auth/google', [GoogleAuthController::class, 'redirect'])
        ->name('google.redirect');

    Route::get('/auth/google/callback', [GoogleAuthController::class, 'callback'])
        ->name('google.callback');

require __DIR__.'/settings.php';
