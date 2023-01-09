<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
	use HasFactory;

	protected $guarded = [];

	public function from()
	{
		return $this->belongsTo(User::class, 'from_id');
	}
}
