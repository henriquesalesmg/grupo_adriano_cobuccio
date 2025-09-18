<?php

namespace App\Http\Controllers;

use App\Http\Requests\TransactionRequest;
use Illuminate\Support\Facades\Auth;
use App\Models\Transaction;

class TransactionController extends Controller
{
    public function index()
    {
        $transactions = Transaction::where('user_id', Auth::id())->orderByDesc('date')->get();
        return view('transactions.index', compact('transactions'));
    }

    public function store(TransactionRequest $request)
    {
        $validated = $request->validated();

        Transaction::create([
            'user_id' => Auth::id(),
            'amount' => $validated['amount'],
            'description' => $validated['description'],
            'date' => $validated['date'],
            'type' => $validated['type'],
        ]);

        return redirect()->route('depositos')->with('success', 'Movimentação cadastrada com sucesso!');
    }
}
