<?php

namespace App\Http\Services;

use App\Models\UserInfo;
use Illuminate\Support\Facades\Auth;
use App\Http\Interfaces\UserInfoInterface;

class UserInfoService implements UserInfoInterface
{
    public function getUserInfo()
    {
        $userId = Auth::id(); 
        return UserInfo::where('user_id', $userId)->first(); 
        
    }

    public function update(array $data)
    {
        $userId = Auth::id();
        $userInfo = UserInfo::firstOrNew(['user_id' => $userId]);

        $userInfo->country_id = $data['country_id'] ?? null;
        $userInfo->city_id = $data['city_id'] ?? null;
        $userInfo->address = $data['address'];
        $userInfo->zip_code = $data['zip_code'] ?? null;
        $userInfo->phone = $data['phone'] ?? null;
        $userInfo->is_finished_profile = 1;

        $userInfo->save();

        return $userInfo;
    }

    public function getAllUserInfo()
    {
        $userInfo = UserInfo::with([
            'user' => function ($query) {
                $query->withCount(['activeProducts']);
            }
        ])
        ->where('is_finished_profile', 1)
        ->orderBy('created_at', 'desc')
        ->get();

        return $userInfo;
    }
}