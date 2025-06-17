<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Company;
use App\Models\UserInfo;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Registered;
use App\Notifications\CustomVerifyEmail;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{

    public function register(Request $request)
    {
    
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'first_name' => 'required',
            'last_name' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

       
        $user = User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'role_id' => $request->role_id,
        ]);

        if (!$user) {
            return response()->json(['message' => 'Something went wrong'], 500);
        }

        if($user->role_id == 2)
        {
            $company = new Company();
            $company->user_id = $user->id;

            $company->save();

        }
        if ($user->role_id == 3) {
            $userInfo = new UserInfo();
            $userInfo->user_id = $user->id;
            $userInfo->save(); // ostala polja ostaju null
        }


        // Pokreni događaj i pošalji custom verifikacioni email
       $user->sendEmailVerificationNotification();

        // Generiši JWT token
        $token = JWTAuth::fromUser($user);

        return response()->json([
            'token' => $token,
            'user' => $user,
            'message' => 'Registration successful. Please check your email for verification link.'
        ]);
    }
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        if (!Hash::check($request->password, $user->password)) {
            return response()->json(['error' => 'Invalid password'], 401);
        }

        $token = JWTAuth::attempt($request->only('email', 'password'));


        return response()->json([
            'success' => 'OK',
            'token' => $token,
            'user' => Auth::user(),
        ], 200);
    }

    public function me()
    {
        return response()->json(Auth::user());
    }
}
