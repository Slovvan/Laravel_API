<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\Comments;
use App\Models\Article;

class CommentNotification extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $comment;
    public $article;

    /**
     * Create a new message instance.
     */
    public function __construct(Comments $comment, Article $article)
    {
        $this->comment = $comment;
        $this->article = $article;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Nuevo comentario en tu artículo: ' . $this->article->title,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.comment-notification',
            with: [
                'comment' => $this->comment,
                'article' => $this->article,
            ],
        );
    
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
