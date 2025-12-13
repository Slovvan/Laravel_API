<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Laravel</title>
    <!-- Bootstrap via CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <header class="bg-dark text-white p-3">
        <h1>Header</h1>
        @guest
            <a href="{{ route('login') }}">Login</a>
            <a href="{{ route('register') }}">Register</a>
        @endguest
        @auth
        <form action="{{route('logout')}}" method="POST">
            @csrf
            <button type="submit">Déconnexion</button>
        </form> 
        <<a href="{{route('profil.show', auth()->user()->profil->id)}}" class="btn btn-link text-white">Mon Profil</a>
        @endauth
    </header>
    

    <main class="container mt-4">
        @include('layouts.flash-messages')
        @auth
            <h1>Bonjour {{Auth::user()->name}}</h1>
            @if(auth()->check() && auth()->user()->is_admin === 'admin')
            <li>
                <a href="{{route('users.index')}}">Liste des Comptes</a>
            </li>
            @endif
            <li>
                <a href="{{route('articles.index')}}">Liste des articles</a>
            </li>
        @endauth

        @yield('content')

    </main>

    <footer class="bg-light text-center p-3">
        <p>Footer</p>
    </footer>
</body>
</html>
