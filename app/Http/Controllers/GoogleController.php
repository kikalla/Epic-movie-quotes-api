<?php

namespace App\Http\Controllers;

use App\Models\User;
use Laravel\Socialite\Facades\Socialite;

class GoogleController extends Controller
{
	public function store()
	{
		$googleUser = Socialite::driver('google')->stateless()->user();

		$user = User::updateOrCreate([
			'google_id' => $googleUser->id,
		], [
			'username'                 => $googleUser->name,
			'email'                    => $googleUser->email,
			'google_token'             => $googleUser->token,
			'google_refresh_token'     => $googleUser->refreshToken,
		]);

		$user->markEmailAsVerified();

		return redirect(env('APP_FRONT_URL') . '/verified');
	}

	public function googleRedirect()
	{
		return Socialite::driver('google')->stateless()->with(['prompt' => 'select_account'])->redirect();
	}
}
