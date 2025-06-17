<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Interfaces\UserInfoInterface;
use App\Http\Requests\UpdateUserInfoRequest;

class UserInfoController extends Controller
{
     protected $userInfoService;

    public function __construct(UserInfoInterface $userInfoService)
    {
        $this->userInfoService = $userInfoService;
    }

    public function showUserInfo()
    {
        $userInfo = $this->userInfoService->getUserInfo();

        return response()->json([
            'success'=>'OK',
            'data'=>$userInfo], 200);
    }

    public function update(UpdateUserInfoRequest $request)
    {
        $userInfo = $this->userInfoService->update($request->validated());

        return response()->json($userInfo, 200);
    }
}
