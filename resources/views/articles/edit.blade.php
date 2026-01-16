@extends('layouts.app')

@section('content')
    <h1>Editer artícle</h1>

    @if ($errors->any())
        <div>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('articles.update', $article->id) }}" method="POST">s
    @csrf
    @method('PUT')

        <div>
            <label for="title">Title:</label>
            <input type="text" id="title" name="title" value="{{ old('title', $article->title) }}" required>
        </div>

        <div>
            <label for="content">Content:</label>
            <textarea id="content" name="content" required>{{ old('content', $article->content) }}</textarea>
        </div>

        <button type="submit">Mise à jour</button>
        <a href="{{ route('articles.show', $article->id) }}">Canceler</a>
    </form>
@endsection
