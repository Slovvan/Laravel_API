<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Comments;

class Article extends Model
{

    protected $fillable = ['title', 'content', 'user_id'];

    /**
     * Inverse one-to-one / many: Article -> User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function comments()
    {
        return $this->hasMany(Comments::class);
    }

    
    public function articleLikes()
    {
        return $this->belongsToMany(User::class, 'article_user_likes')->withTimestamps();
    }
    public function isArticleLikedByUser(int $userId): bool
    {
        if (!$userId) {
            return false;
        }
        return $this->articleLikes()->where('user_id', $userId)->exists();
    }
    public function loggedUserLikes()
    {
        $userId = auth()->id();
        return $this->isArticleLikedByUser($userId);
    }

     public function likesCount()
    {
        return $this->articleLikes()->count();
    }
}

