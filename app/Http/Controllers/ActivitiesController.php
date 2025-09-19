<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\ActivitiesService;

class ActivitiesController extends Controller
{
    public function index(Request $request)
    {
        $userId = Auth::id();
        $filters = [
            'data_inicio' => $request->input('data_inicio'),
            'data_fim' => $request->input('data_fim'),
        ];
        $service = app(ActivitiesService::class);
        $activities = $service->getActivities($userId, $filters);
        return view('activities.index', compact('activities'));
    }
}
