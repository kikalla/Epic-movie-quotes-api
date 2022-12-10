<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddLikeRequest extends FormRequest
{
	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array<string, mixed>
	 */
	public function rules()
	{
		return [
			'quote_id' => 'required',
		];
	}
}
