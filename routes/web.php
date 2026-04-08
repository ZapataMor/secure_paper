<?php

use App\Http\Controllers\Admin\UserManagementController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');
Route::view('/servicios', 'servicios')->name('services');
Route::view('/nosotros', 'nosotros')->name('about');
Route::view('/planes', 'planes')->name('plans');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');
    Route::view('dashboard/planes', 'private.planes')->name('private.planes');
    Route::view('dashboard/cargar-documento', 'private.upload-document')->name('private.upload-document');

    Route::middleware('admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('users', [UserManagementController::class, 'index'])->name('users.index');
    });
});

require __DIR__.'/settings.php';
