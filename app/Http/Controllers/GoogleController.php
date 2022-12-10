<?php

namespace App\Http\Controllers;

use App\Models\User;
use Laravel\Socialite\Facades\Socialite;
use Carbon\Carbon;
use Firebase\JWT\JWT;

class GoogleController extends Controller
{
	public function googleRedirectRegister()
	{
		return Socialite::driver('google')->stateless()->with(['prompt' => 'select_account', 'redirect_uri' => 'http://127.0.0.1:8000/api/auth/callback/register'])->redirect();
	}

	public function googleRedirectLogin()
	{
		return Socialite::driver('google')->stateless()->with(['prompt' => 'select_account', 'redirect_uri' => 'http://127.0.0.1:8000/api/auth/callback/login'])->redirect();
	}

	public function store()
	{
		$redirect_url = config('movie-quotes.app-url') . '/auth/callback/register';
		$googleUser = Socialite::driver('google')->redirectUrl($redirect_url)->stateless()->user();

		$user = User::updateOrCreate([
			'google_id' => $googleUser->id,
		], [
			'username'                 => $googleUser->name,
			'email'                    => $googleUser->email,
			'google_token'             => $googleUser->token,
			'google_refresh_token'     => $googleUser->refreshToken,
		]);

		User::where('google_id', $googleUser->id)->update([
			'email_verified'=> 'verified-google',
		]);

		return redirect(config('movie-quotes.app-front-url') . '/verified');
	}

	public function login()
	{
		$redirect_url = config('movie-quotes.app-url') . '/auth/callback/login';
		$googleUser = Socialite::driver('google')->redirectUrl($redirect_url)->stateless()->user();

		$payload = [
			'exp' => Carbon::now()->addDay(1)->timestamp,
			'uid' => User::where('email', '=', $googleUser->email)->first()->id,
		];

		$jwt = JWT::encode($payload, config('auth.jwt_secret'), 'HS256');

		$cookie = cookie('access_token', $jwt, 30, '/', config('auth.front_end_top_level_domain'), true, true, false, 'Strict');

		return redirect(config('movie-quotes.app-front-url') . '/news-feed')->withCookie($cookie);
	}
}
