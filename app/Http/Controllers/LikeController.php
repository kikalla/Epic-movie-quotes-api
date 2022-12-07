<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddLikeRequest;
use App\Models\Like;
use Illuminate\Http\Request;

class LikeController extends Controller
{
	public function likeDislike(AddLikeRequest $request)
	{
		$usersLikes = Like::where('user_id', $request->user_id)->get();
		$exits = $usersLikes->where('quote_id', $request->quote_id)->first();
		if ($exits)
		{
			$exits->delete();
			return response('deleted', 202);
		}
		else
		{
			$like = Like::create([
				'user_id'  => $request->user_id,
				'quote_id' => $request->quote_id,
			]);

			return response('created', 201);
		}
	}

	public function sendLikes(Request $request)
	{
		$likes = Like::where('quote_id', $request->quote_id)->get();

		if ($likes->where('user_id', $request->user_id)->first())
		{
			$user = true;
		}
		else
		{
			$user = false;
		}

		return [count($likes), $user];
	}
}
