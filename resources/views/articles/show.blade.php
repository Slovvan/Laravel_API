@extends('layouts.app')

@section('content')
    <h1>{{ $article->title }}</h1>

    <p><strong>Autor:</strong> {{ $article->user->name }}</p>
    <p><strong>Date:</strong> {{ $article->created_at->format('d/m/Y H:i') }}</p>

    <div>
        {{ $article->content }}
    </div>

    @auth
        <form action="{{ route('articles.like.toggle', $article )}}" method="POST">
            @csrf
            <button>
                @if($article->isArticleLikedByUser(auth()->user()->id))
                    ❤️ 
                @else
                    🤍
                @endif
                {{ $article->likesCount() }}
                {{ $article->likesCount() == 1 ? 'like' : 'likes'}}
            </button>

        </form>
    @endauth


    @if(auth()->check() && (auth()->user()->is_admin === 'admin' || auth()->id() === $article->user_id))
        <a href="{{ route('articles.edit', $article->id) }}">Editar</a>
    @endif

    @if(auth()->check() && auth()->user()->is_admin === 'admin' || auth()->id() === $article->user_id))
        <form action="{{ route('articles.destroy', $article->id) }}" method="POST" style="display:inline;">
            @csrf
            @method('DELETE')
            <button type="submit">Suprimer artícle</button>
        </form>
    @endif

    <a href="{{ route('articles.index') }}">Index</a>

    <hr>

    <h2>Commentaires</h2>

    @auth
        <form action="{{ route('comments.store', $article->id) }}" method="POST">
            @csrf
            <div>
                <label for="content">Nouveau commentaire:</label>
                <textarea id="content" name="content" required></textarea>
            </div>
            <button type="submit">Comenter</button>
        </form>
    @else
        <p><a href="{{ route('login') }}">Connectez-vous pour commenter</a></p>
    @endauth

    @forelse($article->comments as $comment)
        <div style="border-left: 2px solid #ccc; padding: 10px; margin: 10px 0;">
            @if(auth()->id() !== $comment->user_id)
                @if($comment->isReportedBy(auth()->id()))
                    <p>>commentaire déjà signalé</p>
                @else
                    <a href="{{ route('comments.report.create', $comment) }}" class="btn btn-danger">
                        - Signaler
                    </a>
                @endif
            @endif
            <p><strong><a href="{{route('profil.show', $comment->user->id)}}">{{ $comment->user->name }}</a></strong> - {{ $comment->created_at->format('d/m/Y H:i') }}</p>
            <p>{{ $comment->content }}</p>

            @if(auth()->check() && auth()->user()->is_admin === 'admin' || auth()->id() === $comment->user->id)
                <form action="{{ route('comments.destroy', $comment->id) }}" method="POST" style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit">Suprimer</button>
                </form>
                <a href="{{ route('comments.edit', $comment->id) }}">Editar</a>
            @endif
            
        </div>
    @empty
        <p>Il n'ya pas de commentaires.</p>
    @endforelse
@endsection
