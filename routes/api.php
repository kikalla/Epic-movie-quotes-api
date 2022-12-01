<?php

use App\Http\Controllers\UserController;
use App\Http\Controllers\GoogleController;
use App\Http\Controllers\MailController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::controller(UserController::class)->group(function () {
	Route::post('/register', 'store');
	Route::post('/login', 'login');
	Route::get('/logout', 'logout');
	Route::get('/check-jwt', 'checkJwt')->middleware('JWTauth');
});

Route::controller(GoogleController::class)->group(function () {
	Route::get('/auth/redirect/register', 'googleRedirectRegister');
	Route::get('/auth/redirect/login', 'googleRedirectLogin');
	Route::get('/auth/callback/register', 'store');
	Route::get('/auth/callback/login', 'login');
});

Route::get('/email/verify/{id}/{hash}', [MailController::class, 'verifyEmail'])->name('verification.verify');
