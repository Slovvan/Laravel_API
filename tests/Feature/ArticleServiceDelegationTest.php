<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\User;
use App\Services\ArticleService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

class ArticleServiceDelegationTest extends TestCase
{
    use RefreshDatabase;

    public function test_article_creation_delegates_to_service(): void
    {
        $user = User::factory()->create();

        $article = new Article([
            'title' => 'Test title',
            'content' => 'Test content',
            'user_id' => $user->id,
        ]);
        $article->id = 123;

        $mock = Mockery::mock(ArticleService::class);
        $mock->shouldReceive('create')
            ->once()
            ->withArgs(function (array $data) use ($user) {
                return $data['title'] === 'Test title'
                    && $data['content'] === 'Test content'
                    && $data['user_id'] === $user->id;
            })
            ->andReturn($article);

        $this->app->instance(ArticleService::class, $mock);

        $response = $this->actingAs($user)->post('/articles', [
            'title' => 'Test title',
            'content' => 'Test content',
        ]);

        $response->assertRedirect(route('articles.show', 123));
        $response->assertSessionHas('success');
    }
}
