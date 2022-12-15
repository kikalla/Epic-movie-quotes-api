<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Like;
use App\Models\Movie;
use App\Models\Quote;
use App\Models\User;
use Illuminate\Http\Request;

class SearchController extends Controller
{
	public function sendSearchData(Request $request)
	{
		if (substr($request->search, 0, 1) === '@')
		{
			$movies = Movie::latest()->where('title', 'like', '%' . ltrim($request->search, $request->search[0]) . '%')->get();
			$creatorsImages = [];
			$creatorsusernames = [];

			foreach ($movies as $movie)
			{
				$creatorsImages[] = User::where('id', $movie->user_id)->first()->image;
				$creatorsusernames[] = User::where('id', $movie->user_id)->first()->username;
			}
			return response([$movies, $creatorsImages, $creatorsusernames], 200);
		}

		if (substr($request->search, 0, 1) === '#')
		{
			$quotes = Quote::latest()->where('quote', 'like', '%' . ltrim($request->search, $request->search[0]) . '%')->get();

			$creatorUsernames = [];
			$creatorImages = [];
			$movieTitles = [];
			$quoteLikes = [];
			$quoteLikeds = [];
			$QuoteComments = [];
			$commentUsernames = [];
			$commentsImages = [];
			$commentsShow = [];

			foreach ($quotes as $key=>$quote)
			{
				$user = User::where('id', $quote->user_id)->first();
				$movie = Movie::where('id', $quote->movie_id)->first();
				$comments = Comment::where('quote_id', $quote->id)->get();
				if (count($comments) !== 0)
				{
					foreach ($comments as $comment)
					{
						$QuoteComments[$key][] = $comment->comment;
						$commentUsernames[$key][] = User::where('id', $comment->user_id)->first()->username;
						$commentsImages[$key][] = User::where('id', $comment->user_id)->first()->image;
					}
					$commentsShow[] = true;
				}
				else
				{
					$QuoteComments[$key] = [];
					$commentUsernames[$key] = [];
					$commentsImages[$key] = [];
					$commentsShow[$key] = false;
				}

				$likes = Like::where('quote_id', $quote->id)->get();
				if ($likes->where('user_id', jwtUser()->id)->first())
				{
					$quoteLikeds[] = true;
				}
				else
				{
					$quoteLikeds[] = false;
				}
				$quoteLikes[] = count(Like::where('quote_id', $quote->id)->get());
				$movieTitles[] = $movie->title;
				$creatorUsernames[] = $user->username;
				$creatorImages[] = $user->image;
			}

			return response([$quotes, $creatorUsernames, $creatorImages,
				$movieTitles, $quoteLikes, $quoteLikeds,
				$QuoteComments, $commentUsernames, $commentsImages, $commentsShow,
			], 200);
		}
	}

	public function sendSearchMovies(Request $request)
	{
		$movies = Movie::latest()->where('user_id', jwtUser()->id)->where('title', 'like', '%' . $request->search . '%')->get();
		return response($movies, 200);
	}
}
