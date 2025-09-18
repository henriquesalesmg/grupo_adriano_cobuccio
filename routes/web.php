<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\ForgotPasswordController;

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TransactionController;


// =====================
// Rotas públicas
// =====================
Route::get('/', function () {
    return view('welcome');
});


// =====================
// Rotas de autenticação
// =====================
Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register']);
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// =====================
// Recuperação de senha customizada
// =====================
Route::get('/password/recover', [ForgotPasswordController::class, 'showVerifyForm'])->name('password.request');
Route::post('/password/recover', [ForgotPasswordController::class, 'verify'])->name('password.verify');
Route::post('/password/reset-custom', [ForgotPasswordController::class, 'update'])->name('password.update.custom');
// =====================
// Rotas protegidas por autenticação
// =====================
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    // Movimentações (receitas e despesas)
    Route::get('/transactions', [TransactionController::class, 'index'])->name('depositos');
    Route::post('/transaction', [TransactionController::class, 'store'])->name('movimentacoes.store');
});
