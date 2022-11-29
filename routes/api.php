<?php

// header('Access-Control-Allow-Origin: *');

use App\Http\Controllers\UserController;
use App\Http\Controllers\GoogleController;
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

Route::get('/auth/redirect', [GoogleController::class, 'googleRedirect']);
Route::get('/auth/callback', [GoogleController::class, 'store']);
