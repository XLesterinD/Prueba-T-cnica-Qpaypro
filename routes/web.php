<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DonationController;

Route::get('/', [DonationController::class, 'landing'])->name('landing');
Route::get('/donar', [DonationController::class, 'create'])->name('donar');
Route::post('/donar', [DonationController::class, 'store'])->name('donar.store');
Route::post('/callback', [DonationController::class, 'callback'])->name('qpaypro.callback');
Route::post('/pago', [DonationController::class, 'procesarPago'])->name('pago.procesar');

// Protección básica para el dashboard
Route::get('/dashboard', [DonationController::class, 'dashboard'])->middleware('auth')->name('dashboard');

