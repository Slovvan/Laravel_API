@extends('layouts.app')

@section('content')
    <h1>Creer nouveau artícle</h1>

    @if ($errors->any())
        <div>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('articles.store') }}" method="POST">
        @csrf
        <div>
            <label for="title">Title:</label>
            <input type="text" id="title" name="title" value="{{ old('title') }}" required>
        </div>

        <div>
            <label for="content">Content:</label>
            <textarea id="content" name="content" required>{{ old('content') }}</textarea>
        </div>

        <button type="submit">Creer artícle</button>
        <a href="{{ route('articles.index') }}">Canceler</a>
    </form>
@endsection
