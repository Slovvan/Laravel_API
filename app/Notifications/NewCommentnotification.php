<?php

namespace App\Notifications;

use App\Models\Comments;
use App\Models\Article;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class NewCommentNotification extends Notification
{
    protected $comment;
    protected $article;

    public function __construct(Comments $comment, Article $article)
    {
        $this->comment = $comment;
        $this->article = $article;
    }

    public function via($notifiable): array
    {
        return ['database', 'broadcast'];
    }

    public function broadcastOn()
    {
        return [new PrivateChannel('user.' . $this->article->user_id)];
    }

    public function broadcastAs(): string
    {
        return 'notification.received';
    }

    public function toBroadcast($notifiable): BroadcastMessage
    {
        return new BroadcastMessage([
            'type' => 'comment',
            'data' => [
                'comment_id' => $this->comment->id,
                'article_id' => $this->article->id,
                'article_title' => $this->article->title,
                'commenter_name' => $this->comment->user->name,
                'commenter_id' => $this->comment->user->id,
                'comment_excerpt' => substr($this->comment->content, 0, 50),
                'url' => route('articles.show', $this->article->id),
            ],
        ]);
    }

    public function toArray($notifiable): array
    {
        return [
            'type' => 'comment',
            'comment_id' => $this->comment->id,
            'article_id' => $this->article->id,
            'article_title' => $this->article->title,
            'commenter_name' => $this->comment->user->name,
            'commenter_id' => $this->comment->user->id,
            'comment_excerpt' => substr($this->comment->content, 0, 50),
            'url' => route('articles.show', $this->article->id),
        ];
    }
}