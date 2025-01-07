<?php

namespace App\Http\Controllers;

use App\Models\Movie;
use App\Models\Theater;
use App\Models\Showtime;
use Illuminate\Auth\Events\Validated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MovieController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $movies = Movie::all();
        return view('admin.movie.show', compact('movies'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $theaters = Theater::all();
        return view('admin.movie.insert', compact('theaters'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $movies = new Movie();
        $movies->name = $request->name;
        $movies->desc = $request->desc;

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '.' . $image->getClientOriginalName();
            $image->move(public_path('movies_img'), $imageName);
            $movies->image = 'movies_img/' . $imageName;
        }

        if ($request->hasFile('trailer')) {
            $trailer = $request->file('trailer');
            $trailerName = time() . '.' . $trailer->getClientOriginalName();
            $trailer->move(public_path('movies_trailer'), $trailerName);
            $movies->trailer = 'movies_trailer/' . $trailerName;
        }

        $movies->save();
        return redirect('admin/show-movie');
    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(int $id)
    {
        $movies = Movie::find($id);
        $theaters = Theater::all();
        return view('admin.movie.edit', compact('movies', 'theaters'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $movies = Movie::find($id);
        $movies->name = $request->name;
        $movies->desc = $request->desc;

        if ($request->hasFile('image')) {
            if ($movies->image && file_exists(public_path('movies_img/') . $movies->image)) {
                unlink(public_path('movies_img/') . $movies->image);
            }
            $image = $request->file('image');
            $imageName = time() . '.' . $image->getClientOriginalName();
            $image->move(public_path('movies_img'), $imageName);
            $movies->image = 'movies_img/' . $imageName;
        }

        if ($request->hasFile('trailer')) {

            if ($movies->trailer && file_exists(public_path('movies_trailer/') . $movies->trailer)) {
                unlink(public_path('movies_trailer/') . $movies->trailer);
            }
            $trailer = $request->file('trailer');
            $trailerName = time() . '.' . $trailer->getClientOriginalName();
            $trailer->move(public_path('movies_trailer'), $trailerName);
            $movies->trailer = 'movies_trailer/' . $trailerName;
        }

        $movies->update();
        return redirect('admin/show-movie');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id)
    {
        $movies = Movie::find($id);

        if ($movies->image && file_exists(public_path('movies_img/') . $movies->image)) {
            unlink(public_path('movies_img/') . $movies->image);
        }

        if ($movies->trailer && file_exists(public_path('movies_trailer/') . $movies->trailer)) {
            unlink(public_path('movies_trailer/') . $movies->trailer);
        }

        $movies->delete();
        return redirect('admin/show-movie');
    }
    public function show_trailer(int $id)
    {
        $movies = Movie::findOrFail($id);
        return view('movie-detailes', compact('movies'));
    }
    public function show_movie(int $id)
    {
        $movies = Movie::findOrFail($id);
        $allmovies = Movie::all();
        $movies_datetime = Showtime::findOrFail($id);
       // $movies_theater = Theater::findOrFail($id);
        $movie = Movie::with('comments.user')->findOrFail($id);
        $comments = $movie->comments;
        return view('movie-info', compact('movies', 'movies_datetime', 'movie', 'comments','allmovies'));
    }
    public function show2_movie(int $id)
    {
        $movies = Movie::findOrFail($id);
        $allmovies = Movie::all();
        $movies_datetime = Showtime::findOrFail($id);
        $movies_theater = Theater::findOrFail($id);
        $movie = Movie::with('comments.user')->findOrFail($id);
        $comments = $movie->comments;
        return view('movie-info2', compact('movies', 'movies_datetime', 'movies_theater', 'movie', 'comments','allmovies'));
    }
    public function getTheaters($movieId)
    {
        // Fetch unique theater IDs that have showtimes for the selected movie
        $theaterIds = DB::table('showtimes')
            ->where('movie_id', $movieId)
            ->pluck('theater_id')
            ->unique();

        // Fetch theater names using the theater IDs
        $theaters = DB::table('theaters')
            ->whereIn('id', $theaterIds)
            ->pluck('name', 'id');

        return response()->json($theaters);
    }
    public function getShowDates($movieId, $theaterId)
    {
        // Fetch unique show dates for the selected movie and theater
        $showDates = DB::table('showtimes')
            ->where('movie_id', $movieId)
            ->where('theater_id', $theaterId)
            ->pluck('show_date')
            ->unique();

        return response()->json($showDates);
    }

    public function getShowTimes($movieId, $theaterId, $showDate)
    {
        // Fetch show times for the selected movie, theater, and date
        $showTimes = DB::table('showtimes')
            ->where('movie_id', $movieId)
            ->where('theater_id', $theaterId)
            ->where('show_date', $showDate)
            ->pluck('show_time');

        return response()->json($showTimes);
    }
}
