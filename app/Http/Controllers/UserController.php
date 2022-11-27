<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Http\Requests\AddUserRequest;

class UserController extends Controller
{
	public function store(AddUserRequest $request)
	{
		$user = User::create([
			'username'              => $request->username,
			'email'                 => $request->email,
			'password'              => bcrypt($request->password),
		]);

		return $user;
	}
}
