<?php

namespace App\Http\Requests;

use App\Rules\EmailExsits;
use Illuminate\Foundation\Http\FormRequest;

class LoginUserRequest extends FormRequest
{
	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array<string, mixed>
	 */
	public function rules()
	{
		return [
			'email'                     => ['required', new EmailExsits],
			'password'                  => 'min:8|max:15|required|',
			'remember'                  => 'boolean',
		];
	}
}
