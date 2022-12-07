<?php

use App\Http\Controllers\UserController;
use App\Http\Controllers\GoogleController;
use App\Http\Controllers\MailController;
use App\Http\Controllers\MovieController;
use App\Http\ControlleApp\Http\Controllers\ResetPasswordController;
use App\Http\Controllers\QuoteController;
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

Route::post('/forgot/password', [ResetPasswordController::class, 'forgotPassword'])->name('password.reset');
Route::post('/reset/password', [ResetPasswordController::class, 'resetPassword'])->name('reset.password');

Route::get('/email/verify/{id}/{hash}', [MailController::class, 'verifyEmail'])->name('verification.verify');

Route::controller(MovieController::class)->group(function () {
	Route::post('/movies/add-movie', 'store');
	Route::post('/get-movies', 'sendMovies');
	Route::post('/get-movie', 'sendMovie');
	Route::post('/delete-movie', 'deleteMovie');
	Route::post('/edit-movie', 'editMovie');
});

Route::post('/add-quote', [QuoteController::class, 'store']);
Route::post('/get-quotes', [QuoteController::class, 'sendQuotes']);
Route::post('/get-quote', [QuoteController::class, 'sendQuote']);
Route::post('/edit-quote', [QuoteController::class, 'editQuote']);
Route::post('/delete-quote', [QuoteController::class, 'deleteQuote']);
Route::post('/add-comment', [QuoteController::class, 'comment']);
Route::post('/get-comments', [QuoteController::class, 'sendComments']);
