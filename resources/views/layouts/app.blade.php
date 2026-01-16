<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Tailwind CSS -->
    <link rel="stylesheet" href="{{ asset('css/main.css') }}">
    @yield('extra-css')
</head>
<body class="bg-gray-50">
    <header class="bg-white shadow-md border-b border-gray-200">
        <nav class="max-w-7xl mx-auto px-4 py-4 flex justify-between items-center">
            <div class="flex items-center gap-8">
                <a href="{{ route('welcome') }}" class="text-2xl font-bold text-blue-600">{{ config('app.name', 'Laravel') }}</a>
                @auth
                    <div class="flex gap-6">
                        
                        @if(auth()->check() && auth()->user()->is_admin === 'admin')
                            <a href="{{ route('users.index') }}" class="text-gray-700 hover:text-blue-600 font-medium">Gestion</a>
                        @endif
                    </div>
                @endauth
                <a href="{{ route('articles.index') }}" class="text-gray-700 hover:text-blue-600 font-medium">Articles</a>
            </div>
            
            <div class="flex items-center gap-4">
                @guest
                    <a href="{{ route('loginStore') }}" class="px-4 py-2 text-gray-700 hover:text-blue-600 font-medium">Connexion</a>
                    <a href="{{ route('registerStore') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium">Inscription</a>
                @endguest
                @auth
                    @if(auth()->user()->profil)
                    <img src="{{ auth()->user()->profil->avatar_thumbnail 
                    ? asset('storage/' . auth()->user()->profil->avatar_thumbnail) 
                    : (auth()->user()->profil->avatar 
                        ? asset('storage/' . auth()->user()->profil->avatar) 
                                : asset('images/default-avatar.png')) }}" 
                    alt="Avatar" 
                    style="width: 50px; height: 50px; border-radius: 50%;">
                @else
                    <img src="{{ asset('images/default-avatar.png') }}" 
                        style="width: 50px; height: 50px; border-radius: 50%;">
                @endif
                    @if(auth()->user()->profil)
                        <a href="{{ route('profil.show', auth()->user()->profil->id) }}">{{ Auth::user()->name }}</a>
                    @endif
                    <form action="{{ route('logout') }}" method="POST" style="display:inline;">
                        @csrf
                        <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 font-medium">Déconnexion</button>
                    </form>
                @endauth
            </div>
        </nav>
    </header>

    <main class="max-w-7xl mx-auto px-4 py-8">
        @if(session('success'))
            <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg">
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg">
                {{ session('error') }}
            </div>
        @endif

        @yield('content')
    </main>

    <footer class="bg-gray-800 text-white text-center py-6 mt-12">
        <p>&copy; {{ date('Y') }} {{ config('app.name') }}.</p>
    </footer>
    @stack('scripts')
</body>
</html>
