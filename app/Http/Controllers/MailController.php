<?php

namespace App\Http\Controllers;

use App\Mail\NewEmail;
use App\Mail\SignupEmail;
use Illuminate\Http\Request;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

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
			$user = User::where('email', $check_token->email);
			if ($user->first())
			{
				$user->update([
					'email_verified' => 'verified',
				]);

				DB::table('email_verifications')->where([
					'email'=> $check_token->email,
				])->delete();

				return redirect(config('movie-quotes.app-front-url') . '/verified');
			}
			else
			{
				DB::table('users_emails')->where([
					'email'=> $check_token->email,
				])->update(['email_verified' => 'verified']);

				DB::table('email_verifications')->where([
					'email'=> $check_token->email,
				])->delete();

				return redirect(config('movie-quotes.app-front-url') . '/profile');
			}
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

	public static function addEmail(Request $request)
	{
		if (jwtUser()->email_verified === 'verified-google')
		{
			return 'Cant add email with google registrations';
		}

		if (DB::table('users_emails')->where('email', $request->email)->first())
		{
			return response('Already exists', 403);
		}

		if (jwtUser()->email === $request->email)
		{
			return response('Already exists', 403);
		}

		DB::table('users_emails')->insert([
			'user_id' => jwtUser()->id,
			'email'   => $request->email,
		]);

		$token = Str::random(64);
		DB::table('email_verifications')->updateOrInsert(
			['email'     => $request->email],
			[
				'token'     => $token,
				'created_at'=> Carbon::now(),
			]
		);

		$data = [
			'username'	=> jwtUser()->username,
			'token'   	=> $token,
		];

		Mail::to(jwtUser()->email)->send(new NewEmail($data));

		return response('Email created', 200);
	}

	public function deleteEmail(Request $request)
	{
		$email = DB::table('users_emails')->where('email', $request->email)->first();

		if (!$email)
		{
			return 'Email not exists or its primary';
		}
		if (jwtUser()->id == $email->user_id)
		{
			DB::table('users_emails')->where('email', $request->email)->delete();
			return 'Email deleted';
		}
		else
		{
			return 'Wrong user';
		}
	}

	public function makePrimary(Request $request)
	{
		$email = DB::table('users_emails')->where('email', $request->email)->first();

		if (!$email)
		{
			return response('Email not exists', 404);
		}
		if (jwtUser()->id != $email->user_id)
		{
			return response('Wrong user', 401);
		}
		if ($email->email_verified === 'not-verified')
		{
			return response('Verify first', 401);
		}

		$oldEmail = jwtUser()->email;

		DB::table('users_emails')->where('email', $email->email)->delete();

		jwtUser()->update(['email' => $email->email]);

		DB::table('users_emails')->insert([
			'user_id'          => jwtUser()->id,
			'email'            => $oldEmail,
			'email_verified'   => 'verified',
		]);

		return 'Success';
	}

	public function sendEmails()
	{
		$emails = [];
		$verifieds = [];

		$notPrimaryEmails = DB::table('users_emails')->where('user_id', jwtUser()->id)->get();

		foreach ($notPrimaryEmails as $email)
		{
			$emails[] = $email->email;
			if ($email->email_verified === 'verified')
			{
				$verifieds[] = true;
			}
			else
			{
				$verifieds[] = false;
			}
		}

		$emails[] = jwtUser()->email;
		$verifieds[] = true;

		if (jwtUser()->email_verified === 'verified-google')
		{
			$googleUser = true;
		}
		else
		{
			$googleUser = false;
		}

		return [$emails, $verifieds, $googleUser, jwtUser()->username, jwtUser()->image];
	}
}
