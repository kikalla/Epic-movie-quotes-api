<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddQuoteRequest;
use App\Http\Requests\EditQuoteRequest;
use App\Models\Comment;
use App\Models\Like;
use App\Models\Movie;
use App\Models\Quote;
use App\Models\User;
use Illuminate\Http\Request;

class QuoteController extends Controller
{
	public function store(AddQuoteRequest $request)
	{
		$quote = new Quote();

		$quote->setAttribute('user_id', jwtUser()->id);
		$quote->setAttribute('movie_id', $request->movie_id);
		$quote->setTranslation('quote', 'en', $request->quote_en);
		$quote->setTranslation('quote', 'ka', $request->quote_ka);
		$quote->setAttribute('image', $request->file('image')->store('quoteImages', 'public'));
		$quote->save();

		$movie = Movie::where('id', $request->movie_id)->first();
		$quoteNumber = $movie->quote_number;
		$movie->setAttribute('quote_number', $quoteNumber + 1);
		$movie->save();

		return $quote;
	}

	public function sendQuotes(Request $request)
	{
		$userLiked = [];
		$quoteLikes = [];

		$quotes = Quote::where('movie_id', $request->movie_id)->get();

		foreach ($quotes as $quote)
		{
			$quoteLikes[] = count(Like::where('quote_id', $quote->id)->get());
		}

		foreach ($quotes as $quote)
		{
			$likes = Like::where('quote_id', $quote->id)->get();
			if ($likes->where('user_id', jwtUser()->id)->first())
			{
				$userLiked[] = true;
			}
			else
			{
				$userLiked[] = false;
			}
		}

		if ($quotes)
		{
			return [$quotes, $quoteLikes, $userLiked];
		}
		return response('Quotes not found', 404);
	}

	public function sendQuote(Request $request)
	{
		$quote = Quote::where('id', $request->quote_id)->first();
		$user = User::where('id', $quote->user_id)->first();
		$movie = Movie::where('id', $quote->movie_id)->first();

		if ($quote)
		{
			return [$quote, $user->image, $user->username, $movie->user_id];
		}
		return response('Quote not found', 404);
	}

	public function deleteQuote(Request $request)
	{
		$quote = Quote::where('id', $request->quote_id)->first();
		$movie = Movie::where('id', $quote->movie_id)->first();

		if (jwtUser()->id == $quote->user_id || jwtUser()->id == $movie->user_id)
		{
			$quote->delete();
			$quoteNumber = $movie->quote_number;
			$movie->setAttribute('quote_number', $quoteNumber - 1);
			$movie->save();

			return response('Quote deleted', 200);
		}
		else
		{
			return response('Wrong user or quote', 403);
		}
	}

	public function editQuote(EditQuoteRequest $request)
	{
		$quote = Quote::where('id', $request->quote_id)->first();

		if (jwtUser()->id == $quote->user_id)
		{
			$quote->setTranslation('quote', 'en', $request->quote_en);
			$quote->setTranslation('quote', 'ka', $request->quote_ka);

			if ($request->file('image'))
			{
				$quote->setAttribute('image', $request->file('image')->store('quoteImages', 'public'));
			}
			$quote->save();

			return $quote;
		}
		else
		{
			return response('Wrong user or quote', 403);
		}
	}

	public function comment(Request $request)
	{
		$comment = new Comment();

		$comment->setAttribute('user_id', jwtUser()->id);
		$comment->setAttribute('quote_id', $request->quote_id);
		$comment->setAttribute('comment', $request->comment);

		$comment->save();

		$quote = Quote::where('id', $request->quote_id)->first();
		$commentNumber = $quote->comment_number;
		$quote->setAttribute('comment_number', $commentNumber + 1);
		$quote->save();

		return [$comment, jwtUser()->username, jwtUser()->image];
	}

	public function sendComments(Request $request)
	{
		$usernames = [];
		$comments = Comment::where('quote_id', $request->quote_id)->get();
		$userImages = [];

		foreach ($comments as $comment)
		{
			$usernames[] = User::where('id', $comment->user_id)->first()->username;
			$userImages[] = User::where('id', $comment->user_id)->first()->image;
		}

		return [$comments, $usernames, $userImages];
	}

	public function newsFeedQuotes()
	{
		$creatorUsernames = [];
		$creatorImages = [];
		$movieTitles = [];
		$quoteLikes = [];
		$quoteLikeds = [];
		$QuoteComments = [];
		$commentUsernames = [];
		$commentsImages = [];
		$commentsShow = [];

		$quotes = Quote::latest()->paginate(3);

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

		return [$quotes, $creatorUsernames, $creatorImages,
			$movieTitles, $quoteLikes, $quoteLikeds,
			$QuoteComments, $commentUsernames, $commentsImages, $commentsShow,
		];
	}
}
