<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;



Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/email/resend', [AuthController::class, 'resendVerificationEmail']);


//email verification
Route::get('/email/verify', function () {
    return view('auth.verify-email');
})->middleware('auth')->name('verification.notice');

Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill(); // automatski popunjava email_verified_at
    return response()->json(['message' => 'Email verified']);
})->middleware(['signed'])->name('verification.verify');

//resend
//u bodiju salji citavog usera - objekat
Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();
 
    return response()->json(['message'=>'Verification link sent!'], 200);
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');


Route::middleware(['auth:api', 'verified'])->group(function () {
    Route::get('/me', [AuthController::class, 'me']);
});

Route::get('/test-api', function () {
    return response()->json(['status' => 'API routes are working!']);
});