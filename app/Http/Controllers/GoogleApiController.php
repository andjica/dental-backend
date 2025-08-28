<?php
namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use Laravel\Socialite\Facades\Socialite;

class GoogleApiController extends Controller
{
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }
    
   public function login(Request $request)
    {
        $idToken = $request->input('token');

        // Verifikacija tokena na Google API
        $response = Http::get("https://oauth2.googleapis.com/tokeninfo", [
            'id_token' => $idToken,
        ]);

        if (!$response->ok()) {
            return response()->json(['error' => 'Invalid Google token'], 401);
        }

        $googleUser = $response->json();

        // Nađi ili kreiraj user-a (bez resetovanja role_id!)
        $user = User::where('email', $googleUser['email'])->first();

        if (!$user) {
            $user = User::create([
                'first_name'        => $googleUser['given_name'] ?? '',
                'last_name'         => $googleUser['family_name'] ?? '',
                'email'             => $googleUser['email'],
                'email_verified_at' => now(),
                'is_verified'       => 1,
                'role_id'           => null, // samo prvi put null
                'password'          => bcrypt(Str::random(16)),
                'provider_id'       => $googleUser['sub'],
            ]);
        } else {
            $user->update([
                'first_name'        => $googleUser['given_name'] ?? $user->first_name,
                'last_name'         => $googleUser['family_name'] ?? $user->last_name,
                'email_verified_at' => $user->email_verified_at ?? now(),
                'is_verified'       => 1,
                'provider_id'       => $googleUser['sub'],
            ]);
        }

        // 👇 učitaj relaciju sa UserInfo
        $user->load('userInfo');

        $token = JWTAuth::fromUser($user);

        return response()->json([
            'token' => $token,
            'user'  => $user,
        ]);
    }



    public function handleGoogleCallback(Request $request)
    {
        $code = $request->input('code');

        // Zameni code za access_token kod Google-a
        $response = Http::asForm()->post('https://oauth2.googleapis.com/token', [
            'code'          => $code,
            'client_id'     => config('services.google.client_id'),
            'client_secret' => config('services.google.client_secret'),
            'redirect_uri'  => 'http://localhost:8000/auth/google/callback',
            'grant_type'    => 'authorization_code',
        ]);

        $googleUser = Http::get("https://www.googleapis.com/oauth2/v2/userinfo", [
            'access_token' => $response->json()['access_token'],
        ])->json();

        // Nađi user-a
        $user = User::where('email', $googleUser['email'])->first();

        if (!$user) {
            // Novi user
            $user = User::create([
                'first_name'        => $googleUser['given_name'] ?? '',
                'last_name'         => $googleUser['family_name'] ?? '',
                'email'             => $googleUser['email'],
                'email_verified_at' => now(),
                'is_verified'       => 1,
                'role_id'           => null, // prvi put null
                'password'          => bcrypt(Str::random(16)),
                'provider_id'       => $googleUser['id'],
            ]);
        } else {
            // Postojeći user → update podataka (NE diraj role_id)
            $user->update([
                'first_name'        => $googleUser['given_name'] ?? $user->first_name,
                'last_name'         => $googleUser['family_name'] ?? $user->last_name,
                'email_verified_at' => $user->email_verified_at ?? now(),
                'is_verified'       => 1,
                'provider_id'       => $googleUser['id'],
            ]);
        }

        $user->load('userInfo');

        $token = JWTAuth::fromUser($user);

        // Vrati frontend-u token
        return redirect("http://localhost:5173/login-success?token={$token}");
    }


    
}

