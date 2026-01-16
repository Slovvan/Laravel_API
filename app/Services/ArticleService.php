<?php

namespace App\Services;

use App\Models\Article;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class ArticleService
{
    /**
     * Create an article after processing content.
     *
     * @param  array  $data  (expects 'title','content','user_id', ...)
     * @return Article
     */
    public function create(array $data): Article
    {
        // Prepare content: sanitize basic tags (allow p,br,strong,em,ul,ol,li,a)
        $content = $data['content'] ?? '';
        $allowed = '<p><br><strong><em><ul><ol><li><a>';
        $content = strip_tags($content, $allowed);

        // Estimate reading time (minutes)
        $readTime = $this->estimateReadTime($content);

        // Generate excerpt if missing
        $excerpt = $data['excerpt'] ?? Str::limit(strip_tags($content), 200);

        $attributes = [
            'title' => $data['title'],
            'content' => $content,
            'user_id' => $data['user_id'] ?? null,
        ];

        // Merge other allowed attributes if present
        if (!empty($data['published_at'])) {
            $attributes['published_at'] = $data['published_at'];
        }

        // Create and return the Article
        $article = Article::create($attributes);

        // Log metrics for console/monitoring
        Log::info("Article created: '{$article->title}' | Read time: {$readTime} min | Excerpt length: " . strlen($excerpt) . " chars");

        // Optional: handle tags, featured image, events, etc.

        return $article;
    }

    /**
     * Estimate the read time in minutes (rounded up).
     *
     * @param  string  $content
     * @param  int  $wpm
     * @return int
     */
    public function estimateReadTime(string $content, int $wpm = 200): int
    {
        $plain = strip_tags($content);
        $words = str_word_count($plain);
        return (int) max(1, ceil($words / $wpm));
    }

    /**
     * Utility: create a slug from a title (if needed)
     */
    public function slugify(string $title): string
    {
        return Str::slug($title);
    }

    
}