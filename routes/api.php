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

Route::post('/register', [UserController::class, 'store']);
Route::post('/login', [UserController::class, 'login']);
Route::get('/check-jwt', [UserController::class, 'checkJwt'])->middleware('JWTauth');

Route::get('/auth/redirect', [GoogleController::class, 'googleRedirect']);
Route::get('/auth/callback', [GoogleController::class, 'store']);

Route::get('/email/verify/{id}/{hash}', [MailController::class, 'verifyEmail'])->name('verification.verify');
