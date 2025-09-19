<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\DTOs\DashboardDTO;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $dashboardData = DashboardDTO::fromUser($user);

        return view('dashboard', [
            'user' => $dashboardData->user,
            'saldo' => $dashboardData->saldo,
            'totalTransacoes' => $dashboardData->totalTransacoes,
            'totalTransferencias' => $dashboardData->totalTransferencias,
            'totalReversoes' => $dashboardData->totalReversoes,
            'reversalRequests' => $dashboardData->reversalRequests,
            'ultimaAtividade' => $dashboardData->ultimaAtividade,
            'dashboardData' => $dashboardData
        ]);
    }
}
