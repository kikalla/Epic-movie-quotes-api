<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddQuoteRequest extends FormRequest
{
	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array<string, mixed>
	 */
	public function rules()
	{
		return [
			'user_id'        => 'required',
			'quote_en'       => 'required',
			'quote_ka'       => 'required',
			'movie_id'       => 'required',
			'image'          => 'required|image',
		];
	}
}
