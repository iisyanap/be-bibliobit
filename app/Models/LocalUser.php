<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LocalUser extends Model
{
    use HasFactory;

    protected $primaryKey = 'uid';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = ['uid', 'email', 'username', 'name', 'profile_image', 'is_synced'];

    public function userLibraries(): HasMany
{
    return $this->hasMany(UserLibrary::class, 'user_id', 'uid');
}
}
