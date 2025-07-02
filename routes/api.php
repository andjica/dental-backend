<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuctionController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Auth\Events\Verified;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CityController;
use Illuminate\Support\Facades\Redirect;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\CountryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\UserInfoController;
use PharIo\Manifest\AuthorCollection;

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

    Route::get('/categories', [CategoryController::class, 'index']);
    Route::get('/sub-categories/{categoryId}', [CategoryController::class, 'getSubCategories']);

    Route::get('/company', [CompanyController::class, 'showCompany']);
    Route::post('/company/update', [CompanyController::class, 'update']);

    Route::get('/user-info', [UserInfoController::class, 'showUserInfo']);
    Route::post('/user-info', [UserInfoController::class, 'update']);

    //products
    Route::post('/products', [ProductController::class, 'store']);
    Route::get('/products', [ProductController::class, 'index']);
    Route::get('/products/{userId}', [ProductController::class, 'getByUserId']);   
    Route::get('/product/{id}', [ProductController::class, 'show']);
    Route::post('/product/{id}', [ProductController::class, 'update']);

    //auctions
    Route::post('/auction', [AuctionController::class, 'store']);
    //all auctions for admin
    Route::get('/auctions', [AuctionController::class, 'index']);
    //all auctions by user id
    Route::get('/auctions/{userId}', [AuctionController::class, 'getByUserId']);
    Route::get('/auction/{id}', [AuctionController::class, 'show']);
    Route::post('/auction/update/{id}', [AuctionController::class, 'update']);
    Route::delete('/auctions/{id}', [AuctionController::class, 'destroy']);

    Route::get('/admin/users', [AdminController::class, 'getUsersInfo']);
    Route::get('/admin/companies', [AdminController::class, 'getCompanies']);


    Route::get('/admin/inactive/companies', [AdminController::class, 'getInactiveCompanies']);

    Route::get('/admin/activate/company/{companyId}', [AdminController::class, 'activateCompany']);
    Route::get('/admin/delete/{companyId}', [AdminController::class, 'deleteCompany']);
});

Route::get('/cities/{countryId}', [CityController::class, 'getCitiesByCountry']);

Route::get('/countries', [CountryController::class, 'getCountries']);
Route::get('/country/{countryId}/phone-code', [CountryController::class, 'getPhoneCode']);
 
// Route::get('/country/{countryId}/currency', [CountryController::class, 'getCurrency']);

