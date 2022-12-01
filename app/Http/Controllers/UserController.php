<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Http\Requests\AddUserRequest;
use App\Http\Requests\LoginUserRequest;
use Carbon\Carbon;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\JsonResponse;
use Firebase\JWT\JWT;

class UserController extends Controller
{
	public function store(AddUserRequest $request)
	{
		$user = User::create([
			'username'              => $request->username,
			'email'                 => $request->email,
			'password'              => bcrypt($request->password),
		]);

		event(new Registered($user));

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

		$payload = [
			'exp' => Carbon::now()->addDay(1)->timestamp,
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
}
