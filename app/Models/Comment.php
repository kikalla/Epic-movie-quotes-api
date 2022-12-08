<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
	use HasFactory;

	protected $guarded = [];

	public function user()
	{
		return $this->hasOne(User::class);
	}

	public function quote()
	{
		return $this->hasOne(Quote::class);
	}
}
