<?php

namespace App\Http\Controllers;

use App\Http\Requests\TransactionRequest;
use App\Services\TransactionService;

class TransactionController extends Controller
{
    public function update($id, \Illuminate\Http\Request $request)
    {
        $service = app(TransactionService::class);
        $result = $service->update($id, $request);
        if (isset($result['error'])) {
            return redirect()->back()->withInput()->with('error', $result['error']);
        }
        return redirect()->route('transactions')->with('success', $result['success']);
    }
    public function revert($id)
    {
        $service = app(TransactionService::class);
        $result = $service->revert($id);
        if (isset($result['error'])) {
            return redirect()->route('transactions')->with('error', $result['error']);
        }
        return redirect()->route('transactions')->with('success', $result['success']);
    }
    public function destroy($id)
    {
        $service = app(TransactionService::class);
        $result = $service->destroy($id);
        if (isset($result['error'])) {
            return redirect()->route('transactions')->with('error', $result['error']);
        }
        return redirect()->route('transactions')->with('success', $result['success']);
    }
    public function index()
    {
        $service = app(TransactionService::class);
        $dados = $service->getIndexData();
        return view('transactions.index', $dados);
    }

    public function store(TransactionRequest $request)
    {
        $validated = $request->validated();
        $service = app(TransactionService::class);
        $result = $service->create($validated, $request);
        if (isset($result['error'])) {
            return redirect()->back()->withInput()->with('error', $result['error']);
        }
        return redirect()->route('transactions')->with('success', $result['success']);
    }
}
