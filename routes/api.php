<?php

use App\Http\Controllers\UserController;
use App\Http\Controllers\GoogleController;
use App\Http\Controllers\MailController;
use App\Http\Controllers\MovieController;
use App\Http\Controllers\ResetPasswordController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\QuoteController;
use App\Http\Controllers\SearchController;
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
	Route::post('/register', 'store')->name('user.store');
	Route::post('/login', 'login')->name('login');
	Route::get('/logout', 'logout')->name('logout');
	Route::get('/check-jwt', 'checkJwt')->middleware('JWTauth')->name('jwt.check');
	Route::post('/get-user-info', 'sendUserInfo')->name('user.info');
	Route::post('/edit-user', 'editUser')->name('user.edit')->middleware(['JWTauth', 'mailVerified']);
});

Route::controller(GoogleController::class)->group(function () {
	Route::get('/auth/redirect/register', 'googleRedirectRegister')->name('user.google_register');
	Route::get('/auth/redirect/login', 'googleRedirectLogin')->name('user.google_login');
	Route::get('/auth/callback/register', 'store')->name('user.google_registe_callback');
	Route::get('/auth/callback/login', 'login')->name('user.google_login_callback');
});

Route::post('/forgot/password', [ResetPasswordController::class, 'forgotPassword'])->name('password.reset');
Route::post('/reset/password', [ResetPasswordController::class, 'resetPassword'])->name('reset.password');

Route::controller(MailController::class)->group(function () {
	Route::get('/email/verify/{token}', 'verifyEmail')->name('verification.verify');
	Route::middleware(['JWTauth', 'mailVerified'])->group(function () {
		Route::post('/add-email', 'addEmail')->name('email.add');
		Route::post('/get-emails', 'sendEmails')->name('email.get');
		Route::post('/delete-email', 'deleteEmail')->name('email.delete');
		Route::post('/make-primary', 'makePrimary')->name('email.make_primary');
	});
});

Route::controller(MovieController::class)->group(function () {
	Route::middleware(['JWTauth', 'mailVerified'])->group(function () {
		Route::post('/movies/add-movie', 'store')->name('movie.store');
		Route::post('/get-movies', 'sendMovies')->name('movies.send');
		Route::post('/get-movie', 'sendMovie')->name('movie.send');
		Route::post('/delete-movie', 'deleteMovie')->name('movie.delete');
		Route::post('/edit-movie', 'editMovie')->name('movie.edit');
	});
});

Route::controller(QuoteController::class)->group(function () {
	Route::middleware(['JWTauth', 'mailVerified'])->group(function () {
		Route::post('/add-quote', 'store')->name('quote.store');
		Route::post('/get-quotes', 'sendQuotes')->name('quotes.send');
		Route::post('/get-quote', 'sendQuote')->name('quote.send');
		Route::post('/edit-quote', 'editQuote')->name('quote.edit');
		Route::post('/delete-quote', 'deleteQuote')->name('quote.delete');
		Route::post('/add-comment', 'comment')->name('comment.store');
		Route::post('/get-comments', 'sendComments')->name('comments.send');
		Route::post('/news-feed-quotes', 'newsFeedQuotes')->name('news_feed');
	});
});

Route::middleware(['JWTauth', 'mailVerified'])->group(function () {
	Route::post('/like-dislike', [LikeController::class, 'likeDislike'])->name('like_dislike');
	Route::post('/get-likes', [LikeController::class, 'sendLikes'])->name('likes.send');
	Route::post('/search', [SearchController::class, 'sendSearchData'])->name('search');
	Route::post('/search-movies', [SearchController::class, 'sendSearchMovies'])->name('search_movies');
});
