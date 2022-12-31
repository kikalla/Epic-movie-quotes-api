<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Http\Requests\AddUserRequest;
use App\Http\Requests\EditUserRequest;
use App\Http\Requests\LoginUserRequest;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Firebase\JWT\JWT;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
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

		return response($user, 201);
	}

	public function login(LoginUserRequest $request): JsonResponse
	{
		$email = $request->email;
		$secondaryEmail = DB::table('users_emails')->where('email', $request->email)->first();

		if ($secondaryEmail)
		{
			$email = User::where('id', $secondaryEmail->user_id)->first()->email;
		}

		$authenticated = auth()->attempt(
			[
				'email'    => $email,
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
			'uid' => User::where('email', '=', $email)->first()->id,
		];

		$jwt = JWT::encode($payload, config('auth.jwt_secret'), 'HS256');

		$cookie = cookie('access_token', $jwt, $day * 1440, '/', config('auth.front_end_top_level_domain'), true, true, false, 'Strict');

		return response()->json('success', 200)->withCookie($cookie);
	}

	public function checkJwt(): JsonResponse
	{
		return response()->json(
			['message' => 'authenticated successfully', 'user'    => jwtUser()],
			200
		);
	}

	public function logout(): JsonResponse
	{
		$cookie = cookie('access_token', '', 0, '/', config('auth.front_end_top_level_domain'), true, true, false, 'Strict');

		return response()->json('success', 200)->withCookie($cookie);
	}

	public function editUser(EditUserRequest $request)
	{
		if ($request->file('image'))
		{
			$user = User::where('id', jwtUser()->id)->first();
			$user->setAttribute('image', $request->file('image')->store('usersImages', 'public'));
			$user->save();
		}
		if ($request->username !== jwtUser()->username)
		{
			if (!(User::where('username', $request->username)->first()))
			{
				jwtUser()->update(['username' => $request->username]);
			}
			else
			{
				return response('Username already taken', 422);
			}
		}

		if ($request->password)
		{
			jwtUser()->update(['password'=> Hash::make($request->password)]);
		}

		return response('User data changed', 204);
	}

	public function sendUserInfo()
	{
		if (jwtUser() !== null)
		{
			return response([jwtUser()->image, jwtUser()->username], 200);
		}
		else
		{
			return response('Unauthorized', 401);
		}
	}
}
