<?php

use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\Admin\WorkController;
use App\Http\Controllers\DocumentFileController;
use App\Http\Controllers\PrivateDocumentUploadController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');
Route::view('/servicios', 'servicios')->name('services');
Route::view('/nosotros', 'nosotros')->name('about');
Route::view('/planes', 'planes')->name('plans');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');
    Route::view('dashboard/planes', 'private.planes')->name('private.planes');
    Route::get('dashboard/cargar-documento', [PrivateDocumentUploadController::class, 'index'])->name('private.upload-document');
    Route::post('dashboard/cargar-documento', [PrivateDocumentUploadController::class, 'store'])->name('private.upload-document.store');
    Route::get('dashboard/documentos/{document}/archivo', [DocumentFileController::class, 'show'])->name('documents.file.show');

    Route::middleware('admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('users', [UserManagementController::class, 'index'])->name('users.index');
        Route::get('trabajos', [WorkController::class, 'index'])->name('works.index');
        Route::get('trabajos/{user}', [WorkController::class, 'show'])->name('works.show');
        Route::post('trabajos/{user}/enviar', [WorkController::class, 'store'])->name('works.store');
    });
});

require __DIR__.'/settings.php';
