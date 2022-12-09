<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EditUserRequest extends FormRequest
{
	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array<string, mixed>
	 */
	public function rules()
	{
		return [
			'username'                  => 'min:3|max:15|regex:/^[a-z0-9!@#\$%\^\&*\)\(+=._-]+$/',
			'password'                  => 'min:8|max:15',
			'image'                     => 'image',
		];
	}
}
