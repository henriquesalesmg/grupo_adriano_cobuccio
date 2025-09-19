<?php

namespace App\Http\Controllers;


use App\Services\ReceiptService;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class ReceiptController extends Controller
{
    public function show($type, $id)
    {
        $user = Auth::user();
        $service = app(ReceiptService::class);
        $transaction = $service->getTransactionForReceipt($id, $user->id);
        $data = $service->getReceiptData($transaction, $user);
        $pdf = Pdf::loadView('transactions.receipt_pdf', $data);
        $filename = 'comprovante_' . $data['operacao'] . '_' . $transaction->id . '.pdf';
        return $pdf->download($filename);
    }
}
