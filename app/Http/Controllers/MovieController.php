<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddMovieRequest;
use App\Http\Requests\EditMovieRequest;
use App\Models\Movie;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MovieController extends Controller
{
	public function store(AddMovieRequest $request)
	{
		$movie = new Movie();

		$movie->setAttribute('user_id', $request->user_id);
		$movie->setTranslation('title', 'en', $request->title_en);
		$movie->setTranslation('title', 'ka', $request->title_ka);
		$movie->setTranslation('director', 'en', $request->director_en);
		$movie->setTranslation('director', 'ka', $request->director_ka);
		$movie->setTranslation('description', 'en', $request->description_en);
		$movie->setTranslation('description', 'ka', $request->description_ka);
		$movie->setAttribute('image', $request->file('image')->store('movieImages', 'public'));
		$movie->save();

		return $movie;
	}

	public function sendMovies(Request $request)
	{
		$movies = DB::table('movies')->where('user_id', '=', $request->user_id)->get();
		return $movies;
	}

	public function sendMovie(Request $request)
	{
		$movie = DB::table('movies')->where('id', '=', $request->movie_id)->first();
		if ($movie)
		{
			return $movie;
		}
		return response('Movie not found', 404);
	}

	public function deleteMovie(Request $request)
	{
		$movie = DB::table('movies')->where('id', '=', $request->movie_id)->delete();
		if ($movie)
		{
			return response('Movie deleted', 200);
		}
		return response('Movie not found', 404);
	}

	public function editMovie(EditMovieRequest $request, Movie $movie)
	{
		$movie = $movie->where('id', '=', $request->movie_id)->first();

		$movie->setTranslation('title', 'en', $request->title_en);
		$movie->setTranslation('title', 'ka', $request->title_ka);
		$movie->setTranslation('director', 'en', $request->director_en);
		$movie->setTranslation('director', 'ka', $request->director_ka);
		$movie->setTranslation('description', 'en', $request->description_en);
		$movie->setTranslation('description', 'ka', $request->description_ka);
		if ($request->file('image'))
		{
			$movie->setAttribute('image', $request->file('image')->store('movieImages', 'public'));
		}
		$movie->save();

		return $movie;
	}
}
