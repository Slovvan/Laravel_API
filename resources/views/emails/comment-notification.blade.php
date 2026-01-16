<!DOCTYPE html>
<html>
<head>
    <title>Nuevo comentario en tu artículo</title>
</head>
<body>
    <h1>Nuevo comentario en "{{ $article->title }}"</h1>
    <p><strong>{{ $comment->user->name }}</strong> ha comentado:</p>
    <blockquote>{{ $comment->content }}</blockquote>
    <p><a href="{{ url('/articles/' . $article->id) }}">Ver artículo</a></p>
</body>
</html>