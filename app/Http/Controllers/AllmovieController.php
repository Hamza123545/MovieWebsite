<?php

namespace App\Http\Controllers;
use App\Models\Movie;
use App\Models\Theater;
use App\Models\Showtime;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class AllmovieController extends Controller
{
    public function index()
    {
        $movies = Movie::all();
        $theaters = Theater::all();
        $showtimes = Showtime::all();
        return view('allmovie',compact('movies','theaters','showtimes'));
    }
}
