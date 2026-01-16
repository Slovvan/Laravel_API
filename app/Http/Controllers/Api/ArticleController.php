<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Article;
use Illuminate\Http\JsonResponse;

class ArticleController extends Controller
{
    // 1. Liste des articles publiés
    public function index(): JsonResponse
    {
        $articles = Article::with(['user.profil'])
            ->latest()
            ->paginate(10);

        return response()->json([
            'success' => true,
            'data' => $articles
        ]);
    }

    // 2. Détails d’un article (auteur, likes, comentarios)
    // 4. Nombre de likes par article (incluido en la respuesta)
    public function show($id): JsonResponse
    {
        $article = Article::with(['user.profil', 'comments.user.profil'])
            ->findOrFail($id);

        return response()->json([
            'success' => true,
            'article' => [
                'id' => $article->id,
                'title' => $article->title,
                'content' => $article->content,
                'author' => $article->user->name,
                'likes_count' => $article->likesCount(), // Requerimiento 4
                'created_at' => $article->created_at->format('d/m/Y'),
                'comments' => $article->comments // Requerimiento 2
            ]
        ]);
    }
}