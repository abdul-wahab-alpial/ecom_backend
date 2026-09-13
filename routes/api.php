<?php

use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CountryController;
use App\Http\Controllers\StateController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\CityController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\AuthController;



Route::apiResource('brands', BrandController::class);
Route::apiResource('category', CategoryController::class);
Route::apiResource('country', CountryController::class);
Route::apiResource('company', CompanyController::class);
Route::apiResource('state', StateController::class);
Route::apiResource('city', CityController::class);
Route::apiResource('branch', BranchController::class);
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);


Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();

    return response()->json([
        'message' => 'Email verified successfully.'
    ]);
})->middleware(['auth:sanctum', 'signed'])->name('verification.verify');
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth:sanctum');
Route::post('/user/branches', [AuthController::class, 'assignBranch'])
    ->middleware('auth:sanctum');
