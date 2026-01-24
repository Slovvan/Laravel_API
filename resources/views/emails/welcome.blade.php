<!DOCTYPE html>
<html>
<head>
    <title>Bienvenue !</title>
</head>
<body>
    <h1>Bienvenue {{ $user->name }} !</h1>
    <p>Merci de vous être inscrit sur notre plateforme.</p>
    <p>Nous sommes ravis de vous compter parmi nous !</p>
    <p><a href="{{ url('/') }}">Commencer à explorer</a></p>
</body>
</html>