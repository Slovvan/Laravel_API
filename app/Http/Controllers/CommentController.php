<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\CommentNotification;
use App\Models\Comments;
use App\Models\Article;

class CommentController extends Controller
{
    public function store(Request $request, int $articleId)
    {
        $validatedData = $request->validate([
            'content' => ['required', 'string', 'max:255'],
        ]);

        $comment = new Comments();
        $comment->content = $validatedData['content'];
        $comment->article_id = $articleId;
        $comment->user_id = auth()->id();
        $comment->save();

        

        // Send notification email to article author if not the commenter
        $article = Article::find($articleId);
        if ($article->user_id !== auth()->id()) {
            Mail::to($article->user->email)->send(new CommentNotification($comment, $article));
        }

        return redirect()->route('articles.show', ['id' => $articleId])->with('success', 'Commentaire ajouté avec succès.');
    }

    public function index(){
        $comments = Comments::all();
        return view('comments.index', [
            'comments' => $comments
        ]);
    }

    public function edit($id){
        $comment = Comments::find($id);

        if(!$comment){
            return redirect()->route('comments.index');
        }

        // Check if user can edit this comment
        if (auth()->id() !== $comment->user_id && auth()->user()->is_admin !== 'admin') {
            abort(403, 'Unauthorized');
        }

        return view('comments.edit', compact('comment'))->with('info', 'Vous pouvez modifier les informations du commentaire.');
    }

    public function update(Request $request, int $id) {
        $comment = Comments::find($id);

        if (!$comment) {
            return redirect()->route('articles.index');
        }

        // Check if user can edit this comment
        if (auth()->id() !== $comment->user_id && auth()->user()->is_admin !== 'admin') {
            abort(403, 'Unauthorized');
        }

        $validated_datas = $request->validate([
            'content' => ['required', 'string', 'max:255']
        ]);

        $comment->update($validated_datas);

        return redirect()->route('articles.show', ['id' => $comment->article_id])->with('success', 'Les informations du commentaire ont été mises à jour.');
    }

    public function destroy(int $id)
    {
        $comment = Comments::find($id);

        if (!$comment) {
            // Si es una petición AJAX, devolver JSON
            if (request()->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Commentaire non trouvé'], 404);
            }
            return redirect()->route('articles.index')->with('error', 'commentaire non trouvé.');
        }

        // Verificar autorización
        if (auth()->id() !== $comment->user_id && auth()->user()->is_admin !== 'admin') {
            if (request()->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Non autorisé'], 403);
            }
            return redirect()->back()->with('error', 'Non autorisé');
        }

        $comment->delete();
        
        // Si es una petición AJAX, devolver JSON
        if (request()->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Commentaire supprimé avec succès']);
        }
        
        return redirect()->route('articles.index')->with('success', 'Commentaire supprimé avec succès.');
    }
}
