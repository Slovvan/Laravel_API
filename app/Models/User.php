<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use App\Models\Article;
use App\Models\Comments;
use App\Models\Profils;
use App\Models\Reports;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'two_factor_confirmed_at' => 'datetime',
        ];
    }

    //get articles liked by user
    public function likedArticles()
    {
        return $this->belongsToMany(Article::class, 'article_user_likes')->withTimestamps();
    }
    //add or remove like from article
    public function toggleLikeArticle($article)
    {
        if($this->alreadyLikedArticle($article)){
            //if liked, eliminate from liked articles
            $this->likedArticles()->detach($article->id);
            return false; //unlike
        } else {
            $this->likedArticles()->attach($article->id);
            return true; //like
        }
    }
    //get article that has been liked by user
    public function alreadyLikedArticle($article){
        return $this->likedArticles()->where('article_id', $article->id)->exists();
    }
    /**
     * One-to-one: User -> Article
     */
    public function article()
    {
        return $this->hasMany(Article::class);
    }

    /**
     * One-to-one: User -> Profil
     */
    public function profil()
    {
        return $this->hasOne(Profils::class);
    }

    /**
     * One-to-many: User -> Comments
     */
    public function comments()
    {
        return $this->hasMany(Comments::class);
    }
    /**
     * One-to-many: User -> reports
     */
    public function Reports()
    {
        return $this->hasMany(Reports::class);
    }
}
