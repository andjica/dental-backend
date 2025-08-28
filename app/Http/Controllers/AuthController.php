<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Company;
use App\Models\UserInfo;
use Illuminate\Support\Str;
use App\Mail\VerifyCodeMail;
use App\Mail\VerifyNewEmail;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Models\EmailVerificationCode;
use Illuminate\Auth\Events\Registered;
use App\Notifications\CustomVerifyEmail;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{


    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'first_name' => 'required',
            'last_name' => 'required',
            'role_id' => 'required|integer',
            'device_type' => 'nullable|in:mobile,web',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

       $deviceType = $request->device_type ?? 'web';

        $user = User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'role_id' => $request->role_id,
            'is_verified' => false,
            'device_type' => $deviceType,
            'email_verification_token' => Str::random(60),
        ]);

        if (!$user) {
            return response()->json(['message' => 'Something went wrong'], 500);
        }

        // Role-based insert
        if ($user->role_id == 2) {
            Company::create(['user_id' => $user->id]);
        }

        if ($user->role_id == 3) {
            UserInfo::create(['user_id' => $user->id]);
        }

        // 🔁 Branch based on device_type
        if ($request->device_type === 'mobile') {

            //  MOBILE → četvorocifreni kod
            $code = rand(1000, 9999);

            EmailVerificationCode::create([
                'user_id' => $user->id,
                'code' => $code,
                'expires_at' => now()->addMinutes(3),
            ]);

            Mail::to($user->email)->send(new VerifyCodeMail($code, $user->first_name));

        } else {

            // WEB → klasični verifikacioni email
            $user->sendEmailVerificationNotification();
        }

        $token = JWTAuth::fromUser($user);

        return response()->json([
            'token' => $token,
            'user' => $user,
            'message' => 'Registration successful. Please check your email for verification.',
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

        $token = JWTAuth::attempt($credentials);

        if (!$user->email_verified_at) {
            // Ako nema token ili je prazan, generiši novi
            if (empty($user->email_verification_token)) {
                $user->email_verification_token = Str::random(60);
                $user->save();
            }
        }

        return response()->json([
            'success' => 'OK',
            'token' => $token,
            'user' => $user,
            'verification_hash' => $user->email_verification_token ?? null,
        ], 200);
    }

    public function me()
    {
        return response()->json(Auth::user());
    }


    public function verifyEmail($id, $hash)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        if ($user->email_verified_at) {
            return response()->json(['message' => 'Email already verified.'], 200);
        }

        if ($user->email_verification_token !== $hash) {
            return response()->json(['error' => 'Invalid verification token.'], 400);
        }

        $user->email_verified_at = now();
        $user->email_verification_token = null;
        $user->save();

        return response()->json(['message' => 'Email successfully verified!'], 200);
    }




    public function sendVerificationCode(Request $request)
    {
        $user = Auth::user();

        // Proveri da li je već verifikovan (custom logika)
        if (!is_null($user->email_verified_at)) {
            return response()->json(['message' => 'Email already verified'], 200);
        }

        // Obriši stari kod ako postoji
        EmailVerificationCode::where('user_id', $user->id)->delete();

        // Generiši novi četvorocifreni kod
        $code = rand(1000, 9999);

        // Snimi kod u tabelu
        EmailVerificationCode::create([
            'user_id' => $user->id,
            'code' => $code,
            'expires_at' => Carbon::now()->addMinutes(3),
        ]);

        // Pošalji email
        Mail::to($user->email)->send(new VerifyCodeMail($code, $user->first_name));

        return response()->json(['message' => 'Verification code sent.']);
    }


    public function verifyMobileCode(Request $request)
    {
        // 1) Validacija: email + baš 4 cifre
        $request->validate([
            'email' => ['required','email'],
            'code'  => ['required','digits:4'], // prihvata "1234" ili 1234
        ]);

        // 2) Nađi korisnika
        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        // 3) Uzmi poslednji generisani kod iz email_verification_codes
        $entry = EmailVerificationCode::where('user_id', $user->id)
                    ->latest('id')
                    ->first();

        if (!$entry) {
            return response()->json(['message' => 'No verification code found'], 404);
        }

        // 4) Proveri istekao li je
        if (Carbon::parse($entry->expires_at)->isPast()) {
            return response()->json(['message' => 'Code expired'], 410);
        }
 
        // 5) Uporedi (string–na–string da izbegnemo tip probleme)
        if ((string)$entry->code !== (string)$request->code) {
            return response()->json(['message' => 'Invalid code'], 400);
        }

        // 6) Obeleži korisnika kao verifikovanog
        $user->is_verified = 1;                 // tvoja custom kolona
        $user->email_verified_at = now();       // opcionalno, ako koristiš i ovo
        $user->email_verification_token = null; // očisti token za web verifikaciju, ako postoji
        $user->save();

        // 7) Očisti iskorišćene kodove
        EmailVerificationCode::where('user_id', $user->id)->delete();

        return response()->json(['message' => 'Mobile verified successfully'], 200);
    }



    public function updateEmail(Request $request)
    {
        $user = Auth::user();

        $validator = Validator::make($request->all(), [
            'email' => 'required|email|unique:users,email,' . $user->id,
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

         if ($request->email === $user->email) {
            return response()->json([
                'status' => 'error',
                'message' => 'You entered the same email address as your current one.'
            ], 400);
        }


        // sačuvaj novi email kao pending
        $user->pending_email = $request->email;
        $user->email_verification_token = Str::random(60); // token za verifikaciju
        $user->save();

        // pošalji verifikacioni email
        Mail::to($user->pending_email)->send(new VerifyNewEmail($user));

        return response()->json(['message' => 'Verification link sent to new email address']);
    }

   public function verifyNewEmail($token)
    {
        $user = User::where('email_verification_token', $token)->first();

        if (! $user || ! $user->pending_email) {
            return redirect('http://localhost:5173/login?status=error&message=Invalid+or+expired+link');
        }

        // update email
        $user->email = $user->pending_email;
        $user->pending_email = null;
        $user->email_verification_token = null;
        $user->save();

        // redirect po roli
        $redirects = [
            1 => 'http://localhost:5173/admin/dashboard',
            2 => 'http://localhost:5173/company/settings/profile',
            3 => 'http://localhost:5173/user/settings/profile',
        ];

        // uzmi url na osnovu role
        $url = $redirects[$user->role_id] ?? 'http://localhost:5173/login';

        return redirect($url . '?status=success&message=Email+updated+successfully');
    }






    // 🟢 Change Password
    public function changePassword(Request $request)
    {
        $user = Auth::user();

        $validator = Validator::make($request->all(), [
            'current_password' => 'required',
            'new_password' => 'required|min:6|confirmed', // requires new_password_confirmation
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // ovde mora snake_case
        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json(['error' => 'Current password is incorrect'], 400);
        }

        $user->password = Hash::make($request->new_password);
        $user->save();

        return response()->json(['message' => 'Password changed successfully']);
    }


    // 🟢 Forgot Password
    public function forgotPassword(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $status = Password::sendResetLink(
            $request->only('email')
        );

        if ($status === Password::RESET_LINK_SENT) {
            return response()->json(['message' => __($status)], 200);
        }

        return response()->json(['error' => __($status)], 400);
    }



}
