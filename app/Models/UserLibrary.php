<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserLibrary extends Model
{
    use HasFactory;

    protected $table = 'user_library'; // Tambahkan ini untuk menyesuaikan dengan nama tabel migrasi

    protected $fillable = ['user_id', 'book_id', 'status', 'last_page_read', 'updated_at', 'rating'];

    protected $casts = [
        'updated_at' => 'datetime',
        'rating' => 'float',
    ];

    public function user()
    {
        return $this->belongsTo(LocalUser::class, 'user_id', 'uid');
    }

    public function book()
    {
        return $this->belongsTo(Book::class, 'book_id');
    }
}
