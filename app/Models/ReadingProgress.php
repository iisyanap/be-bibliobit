<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReadingProgress extends Model
{
    use HasFactory;
    protected $table = 'reading_progress';

    protected $fillable = [
        'user_library_id',
        'page_read',
        'recorded_at',
        'user_id',
    ];

    protected $casts = [
        'recorded_at' => 'datetime',
    ];

    public function userLibrary()
    {
        return $this->belongsTo(UserLibrary::class, 'user_library_id');
    }
}
