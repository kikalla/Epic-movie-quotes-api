<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddUserRequest extends FormRequest
{
	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array<string, mixed>
	 */
	public function rules()
	{
		return [
			'username'                  => 'required|min:3|max:15|unique:users,username|regex:/^[a-z0-9!@#\$%\^\&*\)\(+=._-]+$/',
			'email'                     => 'required|unique:users,email|email',
			'password'                  => 'min:8|max:15|required',
			'password_confirmation'     => 'same:password, required',
		];
	}
}
