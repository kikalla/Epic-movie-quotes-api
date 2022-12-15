<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddQuoteRequest;
use App\Http\Requests\CommentRequest;
use App\Http\Requests\EditQuoteRequest;
use App\Http\Requests\MovieRequest;
use App\Http\Requests\QuoteRequest;
use App\Models\Comment;
use App\Models\Like;
use App\Models\Movie;
use App\Models\Quote;
use App\Models\User;

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

		return response($quote, 201);
	}

	public function sendQuotes(MovieRequest $request)
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
			return response([$quotes, $quoteLikes, $userLiked], 200);
		}
		return response('Quotes not found', 404);
	}

	public function sendQuote(QuoteRequest $request)
	{
		$quote = Quote::where('id', $request->quote_id)->first();

		if ($quote)
		{
			$user = User::where('id', $quote->user_id)->first();
			$movie = Movie::where('id', $quote->movie_id)->first();
			return response([$quote, $user->image, $user->username, $movie->user_id], 200);
		}
		return response('Quote not found', 404);
	}

	public function deleteQuote(QuoteRequest $request)
	{
		$quote = Quote::where('id', $request->quote_id)->first();
		$movie = Movie::where('id', $quote->movie_id)->first();

		if (jwtUser()->id == $quote->user_id || jwtUser()->id == $movie->user_id)
		{
			$quote->delete();
			$quoteNumber = $movie->quote_number;
			$movie->setAttribute('quote_number', $quoteNumber - 1);
			$movie->save();

			return response('Quote deleted', 204);
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

			return response($quote, 204);
		}
		else
		{
			return response('Wrong user or quote', 403);
		}
	}

	public function comment(CommentRequest $request)
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

		return response([$comment, jwtUser()->username, jwtUser()->image], 201);
	}

	public function sendComments(QuoteRequest $request)
	{
		$usernames = [];
		$comments = Comment::where('quote_id', $request->quote_id)->get();
		$userImages = [];

		foreach ($comments as $comment)
		{
			$usernames[] = User::where('id', $comment->user_id)->first()->username;
			$userImages[] = User::where('id', $comment->user_id)->first()->image;
		}

		return response([$comments, $usernames, $userImages], 200);
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

		return response([$quotes, $creatorUsernames, $creatorImages,
			$movieTitles, $quoteLikes, $quoteLikeds,
			$QuoteComments, $commentUsernames, $commentsImages, $commentsShow,
		], 200);
	}
}
