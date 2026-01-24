<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\User;
use App\Models\Comments;
use Laravel\Scout\Searchable;

class Article extends Model
{
    use Searchable, HasFactory;

    protected $fillable = ['title', 'content', 'user_id', 'status'];

    /**
     * Get the indexable data array for the model.
     */
    public function toSearchableArray(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'content' => $this->content,
            'created_at' => $this->created_at,
        ];
    }

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

    /**
     * Get the estimated read time in minutes.
     */
    public function getReadTime(): int
    {
        $words = str_word_count(strip_tags($this->content));
        $wpm = 200; // words per minute
        return max(1, (int) ceil($words / $wpm));
    }

    /**
     * Get the excerpt (first 200 chars of plain text).
     */
    public function getExcerpt(): string
    {
        return \Illuminate\Support\Str::limit(strip_tags($this->content), 200);
    }
}
