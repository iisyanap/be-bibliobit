<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserLibrary extends Model
{
    use HasFactory;

    protected $table = 'user_library';
    protected $keyType = 'integer';

    protected $fillable = [
        'user_id', 
        'book_id', 
        'status', 
        'last_page_read', 
        'updated_at', 
        'rating'
    ];

    protected $casts = [
        'updated_at' => 'datetime',
        'rating' => 'float',
    ];

    public $timestamps = true;

    public function user()
    {
        return $this->belongsTo(LocalUser::class, 'user_id', 'uid');
    }

    public function book()
    {
        return $this->belongsTo(Book::class, 'book_id');
    }

    public function notes()
    {
        return $this->hasMany(Note::class, 'user_library_id');
    }

    public function readingProgresses()
    {
        return $this->hasMany(ReadingProgress::class, 'user_library_id');
    }
}
