<?php

namespace App\Http\Controllers;

use App\Models\Theater;
use App\Models\Showtime;
use Illuminate\Auth\Events\Validated;
use Illuminate\Http\Request;

class TheaterController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $theaters = Theater::all();
        
        return view('admin.Theater.show', compact('theaters'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.Theater.insert');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // $this->validate($request,[
        //     'name' => 'required | string | max:100',
        //     'email' => 'required',
        //     'status' => 'required',
        // ]);
        
        $theaters = new Theater();
        $theaters->name = $request->name;
        $theaters->address = $request->address;
        $theaters->status = $request->status;
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '.' . $image->getClientOriginalName();
            $image->move(public_path('theater_img'), $imageName);
            $theaters->image = 'theater_img/' . $imageName;
        }
        $theaters->save();
        return redirect('admin/show-theater');
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
        $theaters = Theater::find($id);
        return view('admin.Theater.edit' , compact('theaters'));

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $theaters = Theater::find($id);
        $theaters->name = $request->name;
        $theaters->address = $request->address;
        $theaters->status = $request->status;
        if ($request->hasFile('image')) {
            if ($theaters->image && file_exists(public_path('theater_img/') . $theaters->image)) {
                unlink(public_path('theater_img/') . $theaters->image);
            }
            $image = $request->file('image');
            $imageName = time() . '.' . $image->getClientOriginalName();
            $image->move(public_path('theater_img'), $imageName);
            $theaters->image = 'theater_img/' . $imageName;
        }
        $theaters->update();
        return redirect('admin/show-theater');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id)
    {
        $theaters = Theater::find($id);
        if ($theaters->image && file_exists(public_path('theater_img/') . $theaters->image)) {
            unlink(public_path('theater_img/') . $theaters->image);
        }
        $theaters->delete();
        return redirect('admin/show-theater');
    }
    public function fetchDates(Request $request)
    {
        $theaterId = $request->input('theater_id');
        
        // Fetch distinct show dates for the selected theater
        $dates = Showtime::where('theater_id', $theaterId)
                        ->distinct('show_date')
                        ->pluck('show_date');
        
        return response()->json(['dates' => $dates]);
    }
}
