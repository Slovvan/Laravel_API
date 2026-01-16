<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Article;
use App\Services\ArticleService;
use App\Repositories\UserRepository;
use Inertia\Inertia;

class ArticleController extends Controller
{
     protected ArticleService $articles;

    public function __construct(ArticleService $articles)
    {
        $this->articles = $articles;
        
    }

    public function create() {
        return view('articles.create');
    }

    public function store(Request $request) {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string']
        ]);

        $article = $this->articles->create(array_merge($validated, [
            'user_id' => auth()->id(),
        ]));

        return redirect()->route('articles.show', $article->id)
            ->with('success', 'Article created.');
    }

    public function index(){
        $articles = Article::with('user.profil')->paginate(10);

        // Si es una petición Inertia, renderizar con Inertia
        if (request()->header('X-Inertia')) {
            return Inertia::render('articles/index', [
                'articles' => $articles->map(fn($article) => [
                    'id' => $article->id,
                    'title' => $article->title,
                    'excerpt' => $article->getExcerpt(),
                    'read_time' => $article->getReadTime(),
                    'created_at' => $article->created_at->format('d/m/Y H:i'),
                    'user' => [
                        'id' => $article->user->id,
                        'name' => $article->user->name,
                        'avatar' => $article->user->profils?->avatar_thumbnail ?? null,
                    ],
                    'likes_count' => $article->likesCount(),
                    'is_liked' => auth()->check() ? $article->isArticleLikedByUser(auth()->id()) : false,
                ]),
                'pagination' => [
                    'current_page' => $articles->currentPage(),
                    'last_page' => $articles->lastPage(),
                    'per_page' => $articles->perPage(),
                    'total' => $articles->total(),
                    'next_page_url' => $articles->nextPageUrl(),
                    'prev_page_url' => $articles->prevPageUrl(),
                ],
            ]);
        }

        return view('articles.index', compact('articles'));
    }

    public function show(int $id)
{
    $article = Article::with(['user.profil', 'comments.user.profil'])->findOrFail($id);
    
    // Si es una petición Inertia, renderizar con Inertia
    if (request()->header('X-Inertia')) {
        return Inertia::render('articles/show', [
            'article' => [
                'id' => $article->id,
                'title' => $article->title,
                'content' => $article->content,
                'read_time' => $article->getReadTime(),
                'excerpt' => $article->getExcerpt(),
                'created_at' => $article->created_at->format('d/m/Y H:i'),
                'user' => [
                    'id' => $article->user->id,
                    'name' => $article->user->name,
                    'avatar' => $article->user->profils?->avatar_thumbnail ?? null,
                ],
                'likes_count' => $article->likesCount(),
                'is_liked' => auth()->check() ? $article->isArticleLikedByUser(auth()->id()) : false,
                'comments' => $article->comments->map(fn($comment) => [
                    'id' => $comment->id,
                    'content' => $comment->content,
                    'user' => [
                        'id' => $comment->user->id,
                        'name' => $comment->user->name,
                        'avatar' => $comment->user->profils?->avatar_thumbnail ?? null,
                    ],
                    'created_at' => $comment->created_at->format('d/m/Y H:i'),
                    'can_edit' => auth()->check() && (auth()->id() === $comment->user_id || auth()->user()->is_admin === 'admin'),
                    'can_delete' => auth()->check() && (auth()->id() === $comment->user_id || auth()->user()->is_admin === 'admin'),
                ]),
            ],
            'can_like' => auth()->check(),
            'can_comment' => auth()->check(),
            'can_edit' => auth()->check() && (auth()->id() === $article->user_id || auth()->user()->is_admin === 'admin'),
        ]);
    }
    
    // Si es una petición normal, renderizar Blade (backward compatibility)
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