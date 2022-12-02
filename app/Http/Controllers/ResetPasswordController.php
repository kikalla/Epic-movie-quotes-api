<?php

namespace App\Http\Controllers;

use App\Http\Requests\ForgotPasswordRequest;
use App\Http\Requests\ResetPasswordRequest;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class ResetPasswordController extends Controller
{
	public function forgotPassword(ForgotPasswordRequest $request)
	{
		$token = Str::random(64);
		DB::table('password_resets')->updateOrInsert(
			['email'     => $request->email],
			[
				'token'     => $token,
				'created_at'=> Carbon::now(),
			]
		);
		$username = User::where('email', $request->email)->first()->username;

		Mail::send('email-reset-password', ['token' => $token, 'username' => $username, 'email' => $request->email], function ($message) use ($request) {
			$message->from(env('MAIL_USERNAME'), 'Epic Movie Quotes');
			$message->to($request->email)->subject('Reset Password');
		});

		return response('success', 200);
	}

	public function resetPassword(ResetPasswordRequest $request)
	{
		$check_token = DB::table('password_resets')->where([
			'email'=> $request->email,
			'token'=> $request->token,
		])->first();

		if (!$check_token)
		{
			return response('Invalid token', 422);
		}
		else
		{
			User::where('email', $request->email)->update([
				'password'=> Hash::make($request->password),
			]);

			DB::table('password_resets')->where([
				'email'=> $request->email,
			])->delete();

			return response('success', 200);
		}
	}
}
