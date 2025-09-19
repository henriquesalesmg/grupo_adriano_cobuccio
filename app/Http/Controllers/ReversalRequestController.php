<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReversalRequestController extends Controller
{
    public function store(Request $request, $transactionId)
    {
        $user = Auth::user();
        $result = app(\App\Services\ReversalRequestService::class)->create($transactionId, $user);
        if (isset($result['error'])) {
            return back()->with('error', $result['error']);
        }
        return back()->with('success', $result['success']);
    }

    public function approve($id)
    {
        $user = Auth::user();
        $result = app(\App\Services\ReversalRequestService::class)->approve($id, $user);
        if (isset($result['error'])) {
            return back()->with('error', $result['error']);
        }
        return back()->with('success', $result['success']);
    }

    public function reject($id)
    {
        $user = Auth::user();
        $result = app(\App\Services\ReversalRequestService::class)->reject($id, $user);
        if (isset($result['error'])) {
            return back()->with('error', $result['error']);
        }
        return back()->with('success', $result['success']);
    }
}
