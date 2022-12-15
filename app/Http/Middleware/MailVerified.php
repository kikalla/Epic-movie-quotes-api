<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Exception;
use Illuminate\Http\Request;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class MailVerified
{
	public function handle(Request $request, Closure $next)
	{
		try
		{
			if (!request()->cookie('access_token'))
			{
				throw new \ErrorException('token is not provided');
			}

			$decoded = JWT::decode(
				request()->cookie('access_token'),
				new Key(config('auth.jwt_secret'), 'HS256')
			);
			$user = User::where('id', $decoded->uid)->first()->email_verified;

			if ($user !== 'not-verified')
			{
				return $next($request);
			}
			return response()->json(['message' => 'Not verified'], 401);
		}
		catch (Exception)
		{
			return response()->json(['message' => 'Not verified'], 401);
		}
	}
}
