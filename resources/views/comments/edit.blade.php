@extends('layouts.app')

@section('content')
    <h1>Editer comment</h1>

    @if ($errors->any())
        <div>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('comments.update', $comment->id) }}" method="POST" style="display:inline;">
        @csrf
        @method('PUT')

        <div>
            <label for="content">Content:</label>
            <textarea id="content" name="content" required>{{ old('content', $comment->content) }}</textarea>
        </div>

        <button type="submit">Mise à jour</button>
       
    </form>
@endsection

