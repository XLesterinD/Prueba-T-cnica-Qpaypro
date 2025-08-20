<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DonationController;

// Página principal (landing)
Route::get('/', [DonationController::class, 'landing'])->name('landing');

// Formulario de donación
Route::get('/donar', [DonationController::class, 'create'])->name('donar');
Route::post('/donar', [DonationController::class, 'store'])->name('donar.store');

// Procesar pago (POST interno de la app)
Route::post('/pago', [DonationController::class, 'procesarPago'])->name('pago.procesar');

// Iniciar checkout en QPayPro
Route::get('/qpaypro/checkout/{donation}', [DonationController::class, 'procesarPago'])
    ->name('qpaypro.checkout');

// Callback de QPayPro (acepta GET y POST para cubrir redirección y notificación)
Route::match(['get', 'post'], '/qpaypro/callback', [DonationController::class, 'callback'])
    ->name('qpaypro.callback');

// Dashboard protegido por autenticación
Route::get('/dashboard', [DonationController::class, 'dashboard'])
    ->middleware('auth')
    ->name('dashboard');


// Ingreso al Dashboard
Route::middleware('auth.basic')->group(function () {
    Route::get('/admin', [DonationController::class, 'dashboard'])->name('dashboard');
});
