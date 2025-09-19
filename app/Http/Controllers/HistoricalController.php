<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\HistoricalService;

class HistoricalController extends Controller
{
    public function index(Request $request)
    {
        $userId = Auth::id();
        $filters = [
            'data_inicio' => $request->input('data_inicio'),
            'data_fim' => $request->input('data_fim'),
        ];
        $service = app(HistoricalService::class);
        $transfers = $service->getTransfers($userId, $filters);
        return view('historical.index', compact('transfers'));
    }
}
