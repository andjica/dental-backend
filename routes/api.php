<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;



Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/email/resend', [AuthController::class, 'resendVerificationEmail']);


Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();
    return response()->json(['message' => 'Email verified successfully.']);
})->middleware(['signed'])->name('verification.verify');


Route::middleware(['auth:api', 'verified'])->group(function () {
    Route::get('/me', [AuthController::class, 'me']);
});

