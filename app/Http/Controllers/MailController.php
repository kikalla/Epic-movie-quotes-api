<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Auth\Events\Verified;

class MailController extends Controller
{
	public static function verifyEmail(Request $request)
	{
		$user = User::find($request->route('id'));

		if ($user->markEmailAsVerified())
		{
			event(new Verified($user));
		}

		return redirect(env('APP_FRONT_URL') . '/verified');
	}
}
