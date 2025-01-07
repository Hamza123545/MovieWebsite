<?php

namespace App\Http\Controllers;
use App\Models\Movie;
use App\Models\Booking;
use App\Models\Theater;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use Illuminate\Http\Request;

class UserController extends Controller
{
     public function show_movies()
    {
        $movies = Movie::all();
        $Theaters = Theater::all();
        return view('index',compact('movies','Theaters'));
    }
    public function logout(Request $request) {
        Auth::logout();
        return redirect('/login');
    }
    public function profile(Request $request){
        $bookings = DB::table('bookings')
        ->join('movies', 'bookings.movie', '=', 'movies.id') // Join the movies table
        ->join('theaters', 'bookings.theater', '=', 'theaters.id') // Join the theaters table
        ->select('bookings.*', 'movies.name as movie_name', 'theaters.name as theater_name') // Select required fields
        ->where('bookings.user_id', Auth::id()) // Filter by the authenticated user ID
        ->get(); // Get all bookings for the authenticated user

    return view('profile', ['bookings' => $bookings]);
    }
    
}
