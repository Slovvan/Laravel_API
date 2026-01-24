<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Article;
use App\Models\Comments;
use App\Notifications\NewCommentNotification;

class TestNotification extends Command
{
    protected $signature = 'test:notification';
    protected $description = 'Test notification creation';

    public function handle()
    {
        $user = User::find(1);
        $article = Article::where('user_id', '<>', $user->id)->first();

        if ($article) {
            $this->info("Testing with User: {$user->name} and Article: {$article->title}");
            
            // Create a comment
            $comment = Comments::create([
                'content' => 'Test notification comment',
                'article_id' => $article->id,
                'user_id' => $user->id
            ]);
            
            $this->info("Comment created: {$comment->id}");
            
            // Send notification
            $article->user->notify(new NewCommentNotification($comment, $article));
            
            $this->info("Notification sent");
            
            // Check if notification was created
            $notificationCount = $article->user->notifications()->count();
            $this->info("Notifications for {$article->user->name}: {$notificationCount}");
            
            if ($notificationCount > 0) {
                $this->line("\n✓ Notification successfully created!");
                $notification = $article->user->notifications()->latest()->first();
                $this->line("Type: {$notification->data['type']}");
                $this->line("Article: {$notification->data['article_title']}");
                $this->line("Commenter: {$notification->data['commenter_name']}");
            } else {
                $this->error("✗ No notification was created!");
            }
        } else {
            $this->error("No suitable article found");
        }
    }
}
