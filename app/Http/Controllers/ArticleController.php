<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Article;

class ArticleController extends Controller
{
    public function create() {
        return view('articles.create');
    }

    public function store(Request $request) {
        $validated_datas = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string']
        ]);

        Article::create([
            'title' => $validated_datas['title'],
            'content' => $validated_datas['content'],
            'user_id' => auth()->id()
        ]);

        return redirect()->route('articles.index')->with('success', 'Artículo creado.');
    }

    public function index(){
        $articles = Article::with('user')->paginate(10);
        return view('articles.index', compact('articles'));
    }

    public function show(int $id) {
        $article = Article::with('user', 'comments.user')->find($id);
        if (!$article) {
            return redirect()->route('articles.index');
        }
        return view('articles.show', compact('article'));
    }

    public function edit(int $id) {
        $article = Article::find($id);
        if (!$article) {
            return redirect()->route('articles.index');
        }
        return view('articles.edit', compact('article'));
    }

    public function update(Request $request, int $id) {
        $article = Article::find($id);
        if (!$article) {
            return redirect()->route('articles.index');
        }

        $validated_datas = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string']
        ]);

        $article->update($validated_datas);
        return redirect()->route('articles.show', $article->id)->with('success', 'Actualizado.');
    }

    public function destroy(int $id)
    {
        $article = Article::find($id);
        if (!$article) {
            return redirect()->route('articles.index');
        }
        $article->delete();
        return redirect()->route('articles.index')->with('success', 'Eliminado.');
    }
}