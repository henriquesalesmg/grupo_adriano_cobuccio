<?php

namespace App\Http\Classes;
use Illuminate\Support\Facades\Auth;
use App\Models\Activity;

class Activities
{
    public static function build($action)
    {
    $user = Auth::user();
    $log = new Activity();
    $log->author = $user?->name ?? 'guest';
    $log->email = $user?->email ?? null;
    $log->user_id = $user?->id ?? null;
    $log->action = $action;
    $log->save();
    }
}
