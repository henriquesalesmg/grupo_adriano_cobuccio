<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\WithdrawRequest;

class WithdrawController extends Controller
{
    public function store(WithdrawRequest $request)
    {
        $validated = $request->validated();
        $userId = Auth::id();
        $result = app(\App\Services\WithdrawService::class)->create($validated, $userId);
        if (isset($result['error'])) {
            return redirect()->back()->withInput()->with('error', $result['error']);
        }
        return redirect()->route('transactions')
            ->with('success', $result['success'])
            ->with('receipt_id', $result['receipt_id'])
            ->with('receipt_type', $result['receipt_type']);
    }
}
