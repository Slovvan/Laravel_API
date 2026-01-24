<?php

namespace Tests\Unit\Unit;

use Tests\TestCase;
use App\Models\Article;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ArticleReadTimeTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        \App\Models\User::factory()->create();
    }

    /**
     * Test read time calculation with 200 words (1 minute)
     */
    public function test_read_time_calculation(): void
    {
        $content = implode(' ', array_fill(0, 200, 'word'));
        
        $article = Article::create([
            'title' => 'Test',
            'content' => $content,
            'user_id' => 1,
        ]);

        $this->assertEquals(1, $article->getReadTime());
    }

    /**
     * Test read time rounding up (250 words = 2 minutes)
     */
    public function test_read_time_rounds_up(): void
    {
        $content = implode(' ', array_fill(0, 250, 'word'));
        
        $article = Article::create([
            'title' => 'Test',
            'content' => $content,
            'user_id' => 1,
        ]);

        $this->assertEquals(2, $article->getReadTime());
    }

    /**
     * Test minimum read time is 1 minute
     */
    public function test_minimum_read_time(): void
    {
        $article = Article::create([
            'title' => 'Short',
            'content' => 'Only a few words here.',
            'user_id' => 1,
        ]);

        $this->assertEquals(1, $article->getReadTime());
    }
}
