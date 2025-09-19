<?php

namespace App\Http\Controllers;

use App\Http\Requests\TransferRequest;
use Illuminate\Support\Facades\Auth;

class TransferController extends Controller
{
    public function store(TransferRequest $request)
    {
        $user = Auth::user();
        $data = $request->validated();
        $result = app(\App\Services\TransferService::class)->create($data, $user);
        if (isset($result['error'])) {
            return redirect()->back()->withInput()->with('error', $result['error']);
        }
        return redirect()->route('transactions')->with('success', $result['success']);
    }
}
