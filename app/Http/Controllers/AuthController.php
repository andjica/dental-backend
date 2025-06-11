<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Auth;
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
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

       
        $user = User::create([
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'role_id' => 2,
        ]);

        if (!$user) {
            return response()->json(['message' => 'Something went wrong'], 500);
        }

        // Pokreni događaj i pošalji custom verifikacioni email
        event(new Registered($user));
        $user->notify(new CustomVerifyEmail);

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

        if (!$token = JWTAuth::attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return response()->json([
            'sucess' => 'OK',
            'token' => $token,
            'user' => Auth::user(),
        ], 200);
    }

    public function me()
    {
        return response()->json(Auth::user());
    }
}
