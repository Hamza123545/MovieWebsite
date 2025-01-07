<?php

namespace App\Http\Controllers;
use App\Models\Booking;
use App\Models\Movie;
use App\Models\Theater;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Mail\BookingMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB; 




class BookingController extends Controller
{
    public function index()
    {
        $movies = Movie::all();
        $theaters = Theater::all();
        return view('booking',compact('movies','theaters'));
    }
    public function info(int $id)
    {
        $selectedmovie = Movie::find($id);
        $movies = Movie::find($id);
        return view('selectedbooking',compact('movies'));
    }
    public function store(Request $request)
    {
        if(Auth::check())
        {
            // Generate random booking number
            $randomNumber = str_pad(mt_rand(10000, 99999), 5, '0', STR_PAD_LEFT);
    
            // Create the booking
            $bookings = new Booking();
            $bookings->name = $request->name;
            $bookings->email = $request->email;
            $bookings->tickets = $request->quantity;
            $bookings->contact = $request->contact;
            $bookings->age = $request->age_range;
            $bookings->movie = $request->movie;
            $bookings->theater = $request->theater;
            $bookings->class = $request->class;
            $bookings->date = $request->show_date;
            $bookings->time = $request->show_time;
            $bookings->booking_number = $randomNumber;
            $bookings->user_id = Auth::id();
            $bookings->save();
    
            // Join movie and theater data
            $joinedDetails = Booking::join('movies', 'bookings.movie', '=', 'movies.id')
                                    ->join('theaters', 'bookings.theater', '=', 'theaters.id')
                                    ->where('bookings.id', $bookings->id) // get the specific booking record
                                    ->select('bookings.*', 'movies.name as movie_name', 'theaters.name as theater_name')
                                    ->first();
    
            // Prepare booking details for the email
            $bookingdetails = [
                'user_name' => $bookings->name,
                'booking_token' => $bookings->booking_number, // Generated token
                'movie_name' => $joinedDetails->movie_name,  // Joined movie title
                'theater_name' => $joinedDetails->theater_name, // Joined theater name
                'show_date' => $bookings->date,
                'show_time' => $bookings->time,
                'user_email' => $bookings->email,
            ];
    
            // Dispatch the email job to the queue
            Mail::to($bookingdetails['user_email'])->send(new BookingMail($bookingdetails));
           
            return response()->json([
                'message' => 'Your booking has been created successfully! Thank you for your booking!',
                'random_number' => $randomNumber // Include the random number in the response
            ]);
        }
        else {
            return response()->json(['message' => 'Please log in first.'], 401);
        }
    }
    
    
    public function getPrice(Request $request)
    {
        // Define the age range prices
        $ageRangePrices = [
            '3-12' => 500,
            '13-19' => 900,
            '20-33' => 1500,
            '34-45' => 1500,
        ];

        // Define the class multipliers
        $classMultipliers = [
            'Regular' => 1.0,
            'Premium' => 1.2,
            'Gold' => 1.5,
        ];

        // Get the inputs from the request
        $ageRange = $request->input('ageRange');
        $class = $request->input('class');
        $quantity = (int) $request->input('quantity');

        // Calculate the base price
        $basePrice = $ageRangePrices[$ageRange] ?? 0;

        // Apply the class multiplier
        $classMultiplier = $classMultipliers[$class] ?? 1.0;
        $totalPrice = $basePrice * $classMultiplier * $quantity;

        // Return the total price as a JSON response
        return response()->json(['totalPrice' => $totalPrice]);
    }
    public function show($id)
    {
        // Eager load movie and its theater relationship
        $booking = DB::table('bookings')
            ->join('movies', 'bookings.movie', '=', 'movies.id') // Join the movies table
            ->join('theaters', 'bookings.theater', '=', 'theaters.id') // Join the theaters table
            ->select('bookings.*', 'movies.name as movie_name', 'theaters.name as theater_name') // Select required fields
            ->where('bookings.id', $id) // Filter by booking ID
            ->first(); // Fetch the first result
        
        if (!$booking) {
            // If booking not found, redirect back with an error message
            return redirect()->back()->with('error', 'Booking not found.');
        }
    
    
        return view('admin.booking_detail', compact('booking'));
    }
  
}
