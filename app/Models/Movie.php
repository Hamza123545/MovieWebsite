<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Movie extends Model
{
    use HasFactory;
    public function theaters()
    {
        return $this->hasMany(Theater::class);
    }
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
}
