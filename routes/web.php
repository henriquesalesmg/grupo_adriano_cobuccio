<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\{RegisterController, ForgotPasswordController, LoginController};
use App\Http\Controllers\{
    DashboardController,
    UserSettingsController,
    TransactionController,
    ReportController,
    DepositController,
    HistoricalController,
    ReversalRequestController,
    WithdrawController,
    TransferController,
    ReceiptController,
    ReversalsController,
    ActivitiesController
};


// Rotas públicas
Route::get('/', function () {
    return view('welcome');
});


// Rotas de autenticação
Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register']);
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Recuperação de senha customizada
Route::get('/password/recover', [ForgotPasswordController::class, 'showVerifyForm'])->name('password.request');
Route::post('/password/recover', [ForgotPasswordController::class, 'verify'])->name('password.verify');
Route::post('/password/reset-custom', [ForgotPasswordController::class, 'update'])->name('password.update.custom');

// Rotas protegidas por autenticação
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    // Movimentações (receitas e despesas)
    Route::get('/transactions', [TransactionController::class, 'index'])->name('transactions');
    Route::post('/transaction', [TransactionController::class, 'store'])->name('movimentacoes.store');
    Route::put('/transaction/{id}', [TransactionController::class, 'update'])->name('movimentacoes.update');
    Route::delete('/transaction/{id}', [TransactionController::class, 'destroy'])->name('movimentacoes.destroy');
    Route::post('/transaction/{id}/revert', [TransactionController::class, 'revert'])->name('movimentacoes.revert');
    // Depósito
    Route::post('/deposit', [DepositController::class, 'store'])->name('deposit.store');
    // Saque
    Route::post('/withdraw', [WithdrawController::class, 'store'])->name('withdraw.store');
    // Transferência
    Route::post('/transfer', [TransferController::class, 'store'])->name('transfer.store');

    // Solicitação de reversão
    Route::post('/reversal-request/{transaction}', [ReversalRequestController::class, 'store'])->name('reversal.request');
    Route::post('/reversal-request/{id}/approve', [ReversalRequestController::class, 'approve'])->name('reversal.approve');
    Route::post('/reversal-request/{id}/reject', [ReversalRequestController::class, 'reject'])->name('reversal.reject');
    // Comprovante PDF
    Route::get('/receipt/{type}/{id}', [ReceiptController::class, 'show'])->name('receipt.pdf')->middleware('auth');

    // Relatórios
    Route::get('/relatorio', [ReportController::class, 'index'])->name('report.index');
    Route::get('/relatorio/pdf', [ReportController::class, 'pdf'])->name('report.pdf');
    Route::get('/relatorio/dashboard-pdf', [ReportController::class, 'dashboardPdf'])->name('report.dashboard.pdf');



    // Histórico de transferências
    Route::get('/historical', [HistoricalController::class, 'index'])->name('historical.index');


    // Minhas solicitações de reversão
    Route::get('/reversals', [ReversalsController::class, 'index'])->name('reversals.index');

    // Histórico de atividades
    Route::get('/activities', [ActivitiesController::class, 'index'])->name('activities.index');

    // Configurações de conta do usuário autenticado
    Route::post('/user/settings', [UserSettingsController::class, 'update'])->name('user.settings.update');
});
