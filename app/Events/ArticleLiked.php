<?php

namespace App\Events;

use App\Models\Article;
use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ArticleLiked implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $article;
    public $user;

    public function __construct(Article $article, User $user)
    {
        $this->article = $article;
        $this->user = $user;
    }

    public function broadcastOn(): array
    {
        return [
            new Channel('article.' . $this->article->id),
        ];
    }

    public function broadcastAs(): string
    {
        return 'article.liked';
    }

    public function broadcastWith(): array
    {
        return [
            'article_id' => $this->article->id,
            'user_name' => $this->user->name,
            'likes_count' => $this->article->likesCount(),
        ];
    }
}