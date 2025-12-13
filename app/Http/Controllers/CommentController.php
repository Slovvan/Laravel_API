<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Comments;

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

        return view('comments.edit', compact('comment'))->with('info', 'Vous pouvez modifier les informations du commentaire.');
    }

    public function update(Request $request, int $id) {
        $comment = Comments::find($id);

        if (!$comment) {
            return redirect()->route('articles.index');
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
            return redirect()->route('articles.index')->with('error', 'commentaire non trouvé.');
        }

        $comment->delete();
        
        //needs route to return to articules show page
        return redirect()->route('articles.index')->with('success', 'Commentaire supprimé avec succès.');
    }
}
