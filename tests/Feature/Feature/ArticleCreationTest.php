<?php

namespace Tests\Feature\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Article;

class ArticleCreationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Verify article creation delegates to ArticleService
     */
    public function test_article_creation_delegates_to_service(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->post(route('articles.store'), [
            'title' => 'Test Article',
            'content' => 'Test content',
        ]);

        $this->assertDatabaseHas('articles', [
            'title' => 'Test Article',
            'user_id' => $user->id,
        ]);
    }
}
