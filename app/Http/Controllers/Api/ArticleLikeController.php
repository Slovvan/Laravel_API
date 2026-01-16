<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Article;
use Illuminate\Http\JsonResponse;

class ArticleLikeController extends Controller
{
    public function toggle(Article $article): JsonResponse
    {
        $user = auth()->user();
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Non authentifié'
            ], 401);
        }

        $liked = $user->toggleLikeArticle($article);
        
        return response()->json([
            'success' => true,
            'liked' => $liked,
            'likes_count' => $article->likesCount()
        ]);
    }
}