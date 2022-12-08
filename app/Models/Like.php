<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Like extends Model
{
	use HasFactory;

	protected $guarded = [];

	public function user()
	{
		return $this->hasMany(User::class);
	}

	public function quote()
	{
		return $this->hasMany(Quote::class);
	}
}
