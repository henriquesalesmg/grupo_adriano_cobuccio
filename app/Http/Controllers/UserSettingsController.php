<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Http\Classes\Activities;
use App\Http\Requests\UserRequest;
use App\DTOs\UserUpdateDTO;

class UserSettingsController extends Controller
{
    public function update(UserRequest $request)
    {
        $user = Auth::user();
        $userDto = UserUpdateDTO::fromRequest($request->validated());

        $updateData = $userDto->getUpdateData();
        foreach ($updateData as $key => $value) {
            $user->{$key} = $value;
        }
        $user->save();

        Activities::build($userDto->getActivityDescription());

        return redirect()->back()->with('success', $userDto->getSuccessMessage());
    }
}
