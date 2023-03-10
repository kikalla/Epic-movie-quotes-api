<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EditMovieRequest extends FormRequest
{
	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array<string, mixed>
	 */
	public function rules()
	{
		return [
			'title_en'       => 'required',
			'title_ka'       => 'required',
			'director_en'    => 'required',
			'director_ka'    => 'required',
			'description_en' => 'required',
			'description_ka' => 'required',
		];
	}
}
