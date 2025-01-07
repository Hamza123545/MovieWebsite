<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Mail\BookingMail;
use Illuminate\Support\Facades\Mail;
use App\Models\Booking;
use Illuminate\Support\Facades\Auth;

class MailController extends Controller
{
   public function index(){
    $bookinginfo = Booking::where('user_id', Auth::id())->get();
    
    Mail::to($bookinginfo->email)->send(new BookingMail($bookinginfo));
    dd('booking created sussecfully');


   }
}
