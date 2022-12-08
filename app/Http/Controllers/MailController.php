<?php

namespace App\Http\Controllers;

use App\Mail\SignupEmail;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class MailController extends Controller
{
	public static function verifyEmail(Request $request)
	{
		$check_token = DB::table('email_verifications')->where([
			'token'=> $request->route('token'),
		])->first();

		if (!$check_token)
		{
			return response('Invalid token', 422);
		}

		if ($request->route('token') === $check_token->token)
		{
			User::where('email', $check_token->email)->update([
				'email_verified'=> 'verified',
			]);

			DB::table('email_verifications')->where([
				'email'=> $check_token->email,
			])->delete();

			return redirect(config('movie-quotes.app-front-url') . '/verified');
		}
		else
		{
			return response('Invalid token', 422);
		}
	}

	public static function sendSingupEmail($username, $email, $token)
	{
		$data = [
			'username'	=> $username,
			'token'   	=> $token,
		];

		Mail::to($email)->send(new SignupEmail($data));
	}
}
