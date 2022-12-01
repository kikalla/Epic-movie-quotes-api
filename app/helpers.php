<?php

use App\Models\User;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

function jwtUser()
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

		return User::find($decoded->uid);
	}
	catch (Exception)
	{
		return null;
	}
}
