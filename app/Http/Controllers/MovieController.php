<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddMovieRequest;
use App\Http\Requests\EditMovieRequest;
use App\Http\Requests\MovieRequest;
use App\Models\Movie;

class MovieController extends Controller
{
	public function store(AddMovieRequest $request)
	{
		$movie = new Movie();

		$movie->setAttribute('user_id', jwtUser()->id);
		$movie->setTranslation('title', 'en', $request->title_en);
		$movie->setTranslation('title', 'ka', $request->title_ka);
		$movie->setTranslation('director', 'en', $request->director_en);
		$movie->setTranslation('director', 'ka', $request->director_ka);
		$movie->setTranslation('description', 'en', $request->description_en);
		$movie->setTranslation('description', 'ka', $request->description_ka);
		$movie->setAttribute('image', $request->file('image')->store('movieImages', 'public'));
		$movie->save();

		return response($movie, 201);
	}

	public function sendMovies()
	{
		$movies = Movie::where('user_id', jwtUser()->id)->get();

		if ($movies)
		{
			return response($movies, 200);
		}
		return response('Movie not found', 404);
	}

	public function sendMovie(MovieRequest $request)
	{
		$movie = Movie::where('id', $request->movie_id)->first();
		if ($movie)
		{
			return response([$movie, jwtUser()->image, jwtUser()->username], 200);
		}
		return response('Movie not found', 404);
	}

	public function deleteMovie(MovieRequest $request)
	{
		$movie = Movie::where('id', $request->movie_id)->first();

		if (jwtUser()->id == $movie->user_id)
		{
			$movie->delete();
			return response('Movie deleted', 204);
		}
		else
		{
			return response('Wrong user or Movie', 403);
		}
	}

	public function editMovie(EditMovieRequest $request)
	{
		$movie = Movie::where('id', $request->movie_id)->first();

		if (jwtUser()->id == $movie->user_id)
		{
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

			return response($movie, 204);
		}
		else
		{
			return response('Wrong user', 403);
		}
	}
}
