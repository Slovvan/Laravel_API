@extends('layouts.app')

@section('content')
    <h1>Artícles</h1>

    @if (session('success'))
        <div>{{ session('success') }}</div>
    @endif

    @auth
        <a href="{{ route('articles.create') }}">Creer nouveau artícle</a>
    @endauth

    <table>
        <thead>
        <tr>
            <th>Title</th>
            <th>Autor</th>
            <th>Date</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        @forelse($articles as $article)
            <tr>
                <td>{{ $article->title }}</td>            
                <td><a href="{{route('profil.show', $article->user->id)}}">{{ $article->user->name }}</a></td>
                <td>{{ $article->created_at->format('d/m/Y') }}</td>
                <td>
                    <a href="{{ route('articles.show', $article->id) }}">Voir</a>
                    @if(auth()->check() && (auth()->user()->is_admin === 'admin' || auth()->id() === $article->user_id))
                        <a href="{{ route('articles.edit', $article->id) }}">Editrr</a>
                    @endif
                    @if(auth()->check() && auth()->user()->is_admin === 'admin')
                        <form action="{{ route('articles.destroy', $article->id) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit">Suprimer</button>
                        </form>
                    @endif
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="4">il n'y a pas des articles.</td>
            </tr>
        @endforelse
        </tbody>
    </table>

    @if ($articles->hasPages())
        <div>
            {{ $articles->links() }}
        </div>
    @endif
@endsection
