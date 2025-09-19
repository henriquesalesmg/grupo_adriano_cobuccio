<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\ReportService;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $service = app(ReportService::class);
        $filters = [
            'data_inicio' => $request->input('data_inicio'),
            'data_fim' => $request->input('data_fim'),
            'tipo' => $request->input('tipo'),
        ];
        $transactions = $service->getTransactionsForReport($filters);
        return view('transactions.report', compact('transactions'));
    }

    public function dashboardPdf()
    {
        $service = app(ReportService::class);
        $transactions = $service->getDashboardTransactions();
        $user = Auth::user();
        $periodo = 'Mês atual e futuras (' . now()->format('m/Y') . ')';
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('transactions.report_pdf', compact('transactions', 'user', 'periodo'));
        return $pdf->download('relatorio_movimentacoes_' . now()->format('Y_m') . '.pdf');
    }

    public function pdf(Request $request)
    {
        $service = app(ReportService::class);
        $filters = [
            'data_inicio' => $request->input('data_inicio'),
            'data_fim' => $request->input('data_fim'),
            'tipo' => $request->input('tipo'),
        ];
        $transactions = $service->getTransactionsForReport($filters);
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('transactions.report_pdf', compact('transactions'));
        return $pdf->download('relatorio_movimentacoes.pdf');
    }
}
