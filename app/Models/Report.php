<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Comments;

class Report extends Model
{

    protected $fillable = ['reason', 'description', 'user_id', 'comment_id'];

    /**
     * Inverse one-to-one / many: Article -> User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function Comment()
    {
        return $this->belongsTo(Comments::class);
    }
}