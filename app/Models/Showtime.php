<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Showtime extends Model
{
    use HasFactory;
    protected $fillable = [
        'movie_id',
        'theater_id',
        'show_date',
        'show_time',
    ];
    public function movie()
    {
        return $this->belongsTo(Movie::class);
    }
    
    public function theater()
    {
        return $this->belongsTo(Theater::class);
    }
}
