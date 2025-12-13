<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Comments;

class Profils extends Model
{
    protected $fillable = [
        'user_id',
        'bio',
        'avatar',
    ];
    /**
     * Inverse one-to-one: Profil -> User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * One-to-many: Profil -> Comments
     */
    public function comments()
    {
        return $this->hasMany(Comments::class);
    }
}
