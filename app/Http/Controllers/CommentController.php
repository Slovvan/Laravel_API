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
        $validated = $request->validate([
            'content' => ['required', 'string', 'max:255']
        ]);

        $comment = new Comments();
        $comment->content = $validated['content'];
        $comment->article_id = $articleId;
        $comment->user_id = auth()->id();
        $comment->save();

        // IMPORTANT: Load the user relationship so the notification can see the name
        $comment->load('user'); 

        $article = Article::with('user')->find($articleId);
        
        if ($article->user_id !== auth()->id()) {
            // This will now write directly to the 'notifications' table
            $article->user->notify(new \App\Notifications\NewCommentNotification($comment, $article));
        }

        return redirect()->back();
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

        $this->authorize('update', $comment);

        return view('comments.edit', compact('comment'))->with('info', 'Vous pouvez modifier les informations du commentaire.');
    }

    public function update(Request $request, int $id) {
        $comment = Comments::find($id);

        if (!$comment) {
            return redirect()->route('articles.index');
        }

        $this->authorize('update', $comment);

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

        $this->authorize('delete', $comment);

        $comment->delete();
        
        // Si es una petición AJAX, devolver JSON
        if (request()->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Commentaire supprimé avec succès']);
        }
        
        return redirect()->route('articles.index')->with('success', 'Commentaire supprimé avec succès.');
    }
}
