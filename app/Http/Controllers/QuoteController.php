<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddQuoteRequest;
use App\Http\Requests\EditQuoteRequest;
use App\Models\Comment;
use App\Models\Movie;
use App\Models\Quote;
use App\Models\User;
use Illuminate\Http\Request;

class QuoteController extends Controller
{
	public function store(AddQuoteRequest $request)
	{
		$quote = new Quote();

		$quote->setAttribute('user_id', $request->user_id);
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
		$quotes = Quote::where('movie_id', $request->movie_id)->get();
		if ($quotes)
		{
			return $quotes;
		}
		return response('Quotes not found', 404);
	}

	public function sendQuote(Request $request)
	{
		$quote = Quote::where('id', $request->quote_id)->first();

		if ($quote)
		{
			return $quote;
		}
		return response('Quote not found', 404);
	}

	public function deleteQuote(Request $request)
	{
		$quote = Quote::where('id', $request->quote_id)->first();
		$movie = Movie::where('id', $quote->movie_id)->first();

		$quote->delete();
		$quoteNumber = $movie->quote_number;
		$movie->setAttribute('quote_number', $quoteNumber - 1);
		$movie->save();

		if ($quote)
		{
			return response('Quote deleted', 200);
		}
		return response('Quote not found', 404);
	}

	public function editQuote(EditQuoteRequest $request)
	{
		$quote = Quote::where('id', $request->quote_id)->first();

		$quote->setTranslation('quote', 'en', $request->quote_en);
		$quote->setTranslation('quote', 'ka', $request->quote_ka);

		if ($request->file('image'))
		{
			$quote->setAttribute('image', $request->file('image')->store('quoteImages', 'public'));
		}
		$quote->save();

		return $quote;
	}

	public function comment(Request $request)
	{
		$comment = new Comment();

		$comment->setAttribute('user_id', $request->user_id);
		$comment->setAttribute('quote_id', $request->quote_id);
		$comment->setAttribute('comment', $request->comment);

		$comment->save();

		$quote = Quote::where('id', $request->quote_id)->first();
		$commentNumber = $quote->comment_number;
		$quote->setAttribute('comment_number', $commentNumber + 1);
		$quote->save();

		$username = User::where('id', $request->user_id)->first()->username;

		return [$comment, $username];
	}

	public function sendComments(Request $request)
	{
		$usernames = [];
		$comments = Comment::where('quote_id', $request->quote_id)->get();

		foreach ($comments as $comment)
		{
			$usernames[] = User::where('id', $comment->user_id)->first()->username;
		}

		return [$comments, $usernames];
	}
}
