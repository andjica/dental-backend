<?php

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Auth\Events\Verified;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CityController;
use Illuminate\Support\Facades\Redirect;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\CountryController;



Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login'])->name("login");
Route::post('/email/resend', [AuthController::class, 'resendVerificationEmail']);


//email verification
Route::get('/email/verify', function () {
    return view('auth.verify-email');
})->middleware('auth:api')->name('verification.notice');

Route::get('/email/verify/{id}/{hash}', function (Request $request, $id, $hash) {
    $user = User::findOrFail($id);

    // Validacija potpisa i hasha
    if (! hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
        return response()->json(['message' => 'Invalid verification link.'], 403);
    }

    if (!$user->hasVerifiedEmail()) {
        $user->markEmailAsVerified();
        event(new Verified($user));
    }

    return Redirect::to('http://localhost:5173/verify-success?email=' . $user->email);
})->middleware(['signed'])->name('verification.verify');


//resend
//u bodiju salji citavog usera - objekat
Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();
 
    return response()->json(['message'=>'Verification link sent!'], 200);
})->middleware(['auth:api', 'throttle:6,1'])->name('verification.send');


Route::middleware(['auth:api', 'verified'])->group(function () {
    Route::get('/me', [AuthController::class, 'me']);

    Route::get('/company', [CompanyController::class, 'showCompany']);
    Route::post('/company/update', [CompanyController::class, 'update']);
});

Route::get('/cities/{countryId}', [CityController::class, 'getCitiesByCountry']);

Route::get('/countries', [CountryController::class, 'getCountries']);
Route::get('/country/{countryId}/phone-code', [CountryController::class, 'getPhoneCode']);
 
// Route::get('/country/{countryId}/currency', [CountryController::class, 'getCurrency']);

