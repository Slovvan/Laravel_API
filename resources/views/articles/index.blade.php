@extends('layouts.app')

@section('content')
    <h1>
        Artícles
        @if($query)
            - Resultados para "{{ $query }}"
        @endif
    </h1>

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
                {{-- Columna: Título --}}
                <td>{{ $article->title }}</td>            
                
                {{-- Columna: Autor con miniatura --}}
                <td style="display: flex; align-items: center; gap: 10px;">
                    @php $p = $article->user->profil; @endphp
                    <img src="{{ ($p && $p->avatar_thumbnail) ? asset('storage/' . $p->avatar_thumbnail) : (($p && $p->avatar) ? asset('storage/' . $p->avatar) : asset('images/default-avatar.png')) }}" 
                         style="width: 35px; height: 35px; border-radius: 50%; object-fit: cover;">
                    <a href="{{route('profil.show', $article->user->id)}}">{{ $article->user->name }}</a>
                </td>
                <td>{{ $article->created_at->format('d/m/Y') }}</td>
                <td>
                    <a href="{{ route('articles.show', $article->id) }}">Voir</a>
                    
                    @if(auth()->check() && (auth()->user()->is_admin === 'admin' || auth()->id() === $article->user_id))
                        <a href="{{ route('articles.edit', $article->id) }}">Editar</a>
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
                <td colspan="4">Il n'y a pas d'articles.</td>
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
