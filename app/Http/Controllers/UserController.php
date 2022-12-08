<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Http\Requests\AddUserRequest;
use App\Http\Requests\LoginUserRequest;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Firebase\JWT\JWT;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class UserController extends Controller
{
	public function store(AddUserRequest $request)
	{
		$user = User::create([
			'username'              => $request->username,
			'email'                 => $request->email,
			'password'              => bcrypt($request->password),
		]);

		$token = Str::random(64);
		DB::table('email_verifications')->updateOrInsert(
			['email'     => $request->email],
			[
				'token'     => $token,
				'created_at'=> Carbon::now(),
			]
		);

		if ($user != null)
		{
			MailController::sendSingupEmail($user->username, $user->email, $token);
		}

		return $user;
	}

	public function login(LoginUserRequest $request): JsonResponse
	{
		$authenticated = auth()->attempt(
			[
				'email'    => $request->email,
				'password' => $request->password,
			]
		);

		if (!$authenticated)
		{
			return response()->json('Wrong password', 422);
		}

		if ($request->remember)
		{
			$day = 3;
		}
		else
		{
			$day = 1;
		}

		$payload = [
			'exp' => Carbon::now()->addDay($day)->timestamp,
			'uid' => User::where('email', '=', request()->email)->first()->id,
		];

		$jwt = JWT::encode($payload, config('auth.jwt_secret'), 'HS256');

		$cookie = cookie('access_token', $jwt, 30, '/', config('auth.front_end_top_level_domain'), true, true, false, 'Strict');

		return response()->json('success', 200)->withCookie($cookie);
	}

	public function checkJwt(): JsonResponse
	{
		return response()->json(
			[
				'message' => 'authenticated successfully',
				'user'    => jwtUser(),
			],
			200
		);
	}

	public function logout(): JsonResponse
	{
		$cookie = cookie('access_token', '', 0, '/', config('auth.front_end_top_level_domain'), true, true, false, 'Strict');

		return response()->json('success', 200)->withCookie($cookie);
	}
}
