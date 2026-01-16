<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Comments;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function store(Request $request, Article $article): JsonResponse
    {
        $validated = $request->validate([
            'content' => ['required', 'string', 'max:500'],
        ]);

        $comment = new Comments();
        $comment->content = $validated['content'];
        $comment->article_id = $article->id;
        $comment->user_id = auth()->id();
        $comment->save();

        // Cargar relación user
        $comment->load('user');
        $comment->load('user.profil');

        return response()->json([
            'success' => true,
            'comment' => [
                'id' => $comment->id,
                'content' => $comment->content,
                'user_id' => $comment->user_id,
                'user' => [
                    'id' => $comment->user->id,
                    'name' => $comment->user->name,
                ],
                'created_at' => $comment->created_at->format('d/m/Y H:i'),
            ]
        ], 201);
    }

    // Añade este método a tu CommentController actual
    public function index(Article $article): JsonResponse
    {
        // 3. Liste des commentaires d’un article
        $comments = $article->comments()->with('user.profil')->get();

        return response()->json([
            'success' => true,
            'comments' => $comments
        ]);
    }

    public function update(Request $request, Comments $comment): JsonResponse
    {
        // Verificar autorización
        if (auth()->id() !== $comment->user_id && auth()->user()->is_admin !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Non autorisé'
            ], 403);
        }

        $validated = $request->validate([
            'content' => ['required', 'string', 'max:500'],
        ]);

        $comment->update($validated);

        return response()->json([
            'success' => true,
            'comment' => [
                'id' => $comment->id,
                'content' => $comment->content,
            ]
        ]);
    }

    public function destroy(Comments $comment): JsonResponse
    {
        // Verificar autorización
        if (auth()->id() !== $comment->user_id && auth()->user()->is_admin !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Non autorisé'
            ], 403);
        }

        $comment->delete();

        return response()->json([
            'success' => true,
            'message' => 'Commentaire supprimé'
        ]);
    }
}