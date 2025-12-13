<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Profils;
use App\Models\Report;

class Comments extends Model
{
    protected $fillable = ['content', 'article_id', 'user_id'];

    /**
     * Inverse relation: Comment -> User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Inverse relation: Comment -> Profil
     */
    public function profil()
    {
        return $this->belongsTo(Profils::class);
    }



    public function report()
    {
        return $this->hasMany(Report::class, 'comment_id');
    }
    public function reportCount()
    {
        return $this->report()->count();
    }
    public function isReportedBy($userId)
    {
        return $this->report()->where('user_id', $userId)->exists();
    }

}
