<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 'author', 'genre', 'year', 'description', 'isbn', 'pages', 'publisher', 'cover_photo_path'
    ];
}
