<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Quote extends Model
{
	use HasFactory, HasTranslations;

	protected $guarded = [];

	public $translatable = ['quote'];

	public function user()
	{
		return $this->hasOne(User::class);
	}

	public function movie()
	{
		return $this->hasOne(Movie::class);
	}

	public function comment()
	{
		return $this->hasMany(Movie::class);
	}
}
