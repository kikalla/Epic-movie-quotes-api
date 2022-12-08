<?php

use App\Http\Controllers\UserController;
use App\Http\Controllers\GoogleController;
use App\Http\Controllers\MailController;
use App\Http\Controllers\MovieController;
use App\Http\Controllers\ResetPasswordController;
use App\Http\Controllers\LikeController;
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

Route::get('/email/verify/{token}', [MailController::class, 'verifyEmail'])->name('verification.verify');
Route::post('/add-email', [MailController::class, 'addEmail']);
Route::post('/delete-email', [MailController::class, 'deleteEmail']);
Route::post('/make-primary', [MailController::class, 'makePrimary']);
Route::post('/get-emails', [MailController::class, 'sendEmails']);

Route::controller(MovieController::class)->group(function () {
	Route::post('/movies/add-movie', 'store');
	Route::post('/get-movies', 'sendMovies');
	Route::post('/get-movie', 'sendMovie');
	Route::post('/delete-movie', 'deleteMovie');
	Route::post('/edit-movie', 'editMovie');
});

Route::controller(QuoteController::class)->group(function () {
	Route::post('/add-quote', 'store');
	Route::post('/get-quotes', 'sendQuotes');
	Route::post('/get-quote', 'sendQuote');
	Route::post('/edit-quote', 'editQuote');
	Route::post('/delete-quote', 'deleteQuote');
	Route::post('/add-comment', 'comment');
	Route::post('/get-comments', 'sendComments');
});

Route::post('/like-dislike', [LikeController::class, 'likeDislike']);
Route::post('/get-likes', [LikeController::class, 'sendLikes']);
