<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Movie;
use App\Models\Theater;
use App\Models\Showtime;
use App\Models\Booking;
use App\Models\Contact;

use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function dashboard()
    {
        $users = User::where('role', 'user')->count();
        $movies = Movie::count();
        $theaters = Theater::count();
        $bookings = Booking::count();
        return view('admin.dashboard', compact('users', 'movies', 'theaters', 'bookings'));
    }
    public function users()
    {

        $users = User::where('role', 'user')->get();
        return view('admin.users', compact('users'));
    }

    public function showbookings()
    {

        $bookings = Booking::join('movies', 'bookings.movie', '=', 'movies.id')
        ->join('theaters', 'bookings.theater', '=', 'theaters.id')
        ->select('bookings.*', 'movies.name as movie_name', 'theaters.name as theater_name')
        ->get();

        return view('admin.show-bookings', compact('bookings'));
    }
    public function showcontacts()
    {

        $contacts = Contact::all();
        $contactscount = Contact::count();
        return view('admin.show-contacts', compact('contacts', 'contactscount'));
    }


    public function create()
    {
        $movies = Movie::all();
        $theaters = Theater::all();
        return view('admin.Shows.insert', compact('movies', 'theaters'));
    }
    public function store(Request $request)
    {
        $request->validate([
            'movie_id' => 'required|integer',
            'theater_id' => 'required|integer',
            'show_date' => 'required|date',
            'show_time' => 'required|date_format:H:i',
        ]);

        Showtime::create($request->all());

        return redirect('admin/show-showtime');
    }
    public function show()
    {
        $showtimes = Showtime::with('movie', 'theater')->get();
        return view('admin.shows.show', compact('showtimes'));
    }
    public function edit(int $id)
    {
        $showtimes = Showtime::find($id);
        $movies = Movie::all();
        $theaters = Theater::all();
        return view('admin.shows.edit', compact('showtimes', 'movies', 'theaters'));
    }
    public function update(Request $request, string $id)
    {
        $showtimes = Showtime::find($id);
        $showtimes->update([
            'movie_id' => $request->movie_id,
            'theater_id' => $request->theater_id,
            'show_date' => $request->show_date,
            'show_time' => $request->show_time,
        ]);

        return redirect('admin/show-showtime');
    }
    public function destroy(int $id)
    {
        $showtimes = Showtime::find($id);
        $showtimes->delete();
        return redirect('admin/show-showtime');
    }
    public function fetchTimes(Request $request)
    {
        $theaterId = $request->input('theater_id');
        $date = $request->input('date');

        // Fetch show times for the selected theater and date
        $times = Showtime::where('theater_id', $theaterId)
            ->where('show_date', $date)
            ->pluck('show_time');

        return response()->json(['times' => $times]);
    }
}
