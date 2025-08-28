<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\UserInfo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{

    public function updateRole(Request $request)
    {
    $request->validate([
        'role_id' => 'required|in:2,3'
    ]);

   $user = Auth::user();
    $user->role_id = $request->role_id;
    $user->save();

    if ($user->role_id == 2 && !$user->company) {
        Company::create(['user_id' => $user->id]);
    }

    if ($user->role_id == 3 && !$user->userInfo) {
        UserInfo::create(['user_id' => $user->id]);
    }
    return response()->json([
        'success' => true,
        'message' => 'Role updated successfully',
        'user' => $user
    ]);
}

}
