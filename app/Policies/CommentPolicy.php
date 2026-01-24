<?php

namespace App\Policies;

use App\Models\Comments;
use App\Models\User;
use App\Models\Article;
use Illuminate\Auth\Access\Response;

class CommentPolicy
{
    /**
     * Determine whether the user can view any comments.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view a comment.
     */
    public function view(User $user, Comments $comment): bool
    {
        return true;
    }

    /**
     * Determine whether the user can create comments.
     * Any authenticated user can create comments.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update a comment.
     * Only the comment author or admins can update.
     */
    public function update(User $user, Comments $comment): bool
    {
        if ($this->isAdmin($user)) {
            return true;
        }

        $isAuthor = $user->id === $comment->user_id;
        $withinEditWindow = $comment->created_at?->diffInMinutes(now()) <= 15;
        $notReported = $comment->reportCount() < 3;

        return $isAuthor && $withinEditWindow && $notReported;
    }

    /**
     * Determine whether the user can delete a comment.
     * Only the comment author or admins can delete.
     */
    public function delete(User $user, Comments $comment): bool
    {
        if ($this->isAdmin($user)) {
            return true;
        }

        if ($user->id === $comment->user_id) {
            return true;
        }

        $article = Article::find($comment->article_id);
        return $article && $article->user_id === $user->id;
    }

    /**
     * Determine whether the user can restore a comment.
     * Only admins can restore.
     */
    public function restore(User $user, Comments $comment): bool
    {
        return $user->is_admin === 'admin';
    }

    /**
     * Determine whether the user can permanently delete a comment.
     * Only admins can force delete.
     */
    public function forceDelete(User $user, Comments $comment): bool
    {
        return $user->is_admin === 'admin';
    }

    /**
     * Helper method to check if user is the comment author or admin
     */
    private function isCommentAuthorOrAdmin(User $user, Comments $comment): bool
    {
        return $user->id === $comment->user_id || $user->is_admin === 'admin';
    }

    private function isAdmin(User $user): bool
    {
        return $user->is_admin === 'admin';
    }
}
