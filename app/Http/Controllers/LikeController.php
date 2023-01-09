<?php

namespace App\Http\Controllers;

use App\Events\NotificationCreated;
use App\Http\Requests\AddLikeRequest;
use App\Models\Like;
use App\Models\Notification;
use App\Models\Quote;
use Illuminate\Http\Request;

class LikeController extends Controller
{
	public function likeDislike(AddLikeRequest $request)
	{
		$usersLikes = Like::where('user_id', jwtUser()->id)->get();
		$exits = $usersLikes->where('quote_id', $request->quote_id)->first();
		if ($exits)
		{
			$exits->delete();
			return response('deleted', 204);
		}
		else
		{
			$like = Like::create([
				'user_id'  => jwtUser()->id,
				'quote_id' => $request->quote_id,
			]);
			$quote = Quote::where('id', $request->quote_id)->first();
			if ($quote->user_id !== jwtUser()->id)
			{
				$notification = Notification::create([
					'type'       => 'like',
					'from_id'    => jwtUser()->id,
					'to_id'      => $quote->user_id,
					'quote_id'   => $request->quote_id,
					'is_read'    => false,
				]);
				event((new NotificationCreated($notification->load('from'))));

			}

			return response('created', 201);
		}
	}

	public function sendLikes(Request $request)
	{
		$likes = Like::where('quote_id', $request->quote_id)->get();

		if ($likes->where('user_id', jwtUser()->id)->first())
		{
			$user = true;
		}
		else
		{
			$user = false;
		}

		return response([count($likes), $user], 200);
	}
}
