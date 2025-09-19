<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\ReversalRequest;

class ReversalsController extends Controller
{
    public function index(Request $request)
    {
        $userId = Auth::id();
        $query = ReversalRequest::with(['transaction', 'receiver.account'])
            ->where('requester_id', $userId);

        if ($request->filled('data_inicio')) {
            $query->whereDate('created_at', '>=', $request->data_inicio);
        }
        if ($request->filled('data_fim')) {
            $query->whereDate('created_at', '<=', $request->data_fim);
        }

        $reversals = $query->orderByDesc('created_at')->get();
        return view('reversals.index', compact('reversals'));
    }
}
