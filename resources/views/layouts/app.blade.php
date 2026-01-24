<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" @auth data-user-id="{{ auth()->id() }}" @endauth>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Tailwind CSS -->
    <link rel="stylesheet" href="{{ asset('css/main.css') }}">
    @vite(['resources/js/echo-blade.ts'])
    @yield('extra-css')
</head>
<body class="bg-gray-50">
    <header class="bg-white shadow-md border-b border-gray-200">
        <nav class="max-w-7xl mx-auto px-4 py-4 flex justify-between items-center gap-8">
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

            <!-- Search Bar -->
            <form action="{{ route('search') }}" method="GET" style="flex: 1; max-width: 300px;">
                <input 
                    type="text" 
                    name="q" 
                    placeholder="Buscar artículos..." 
                    value="{{ request('q') }}"
                    style="
                        width: 100%;
                        padding: 8px 12px;
                        border: 1px solid #ddd;
                        border-radius: 4px;
                        font-size: 0.9em;
                    "
                >
            </form>
                        
            <div class="flex items-center gap-4">
    @guest
        <a href="{{ route('loginStore') }}" class="px-4 py-2 text-gray-700 hover:text-blue-600 font-medium">Connexion</a>
        <a href="{{ route('registerStore') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium">Inscription</a>
    @endguest
    @auth
        <!-- Icono de notificaciones -->
        <div style="position: relative;">
            <a href="{{ route('notifications.index') }}" style="position: relative; display: inline-block;">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width: 28px; height: 28px; color: #4b5563;">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
                </svg>
                @if(auth()->user()->unreadNotifications->count() > 0)
                    <span id="notification-badge" style="
                        position: absolute;
                        top: -5px;
                        right: -5px;
                        background: #ef4444;
                        color: white;
                        border-radius: 50%;
                        width: 20px;
                        height: 20px;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        font-size: 11px;
                        font-weight: bold;
                    ">
                        {{ auth()->user()->unreadNotifications->count() }}
                    </span>
                @endif
            </a>
        </div>

        <!-- Avatar del usuario -->
        @if(auth()->user()->profil)
            <img src="{{ auth()->user()->profil->avatar_thumbnail 
                ? asset('storage/' . auth()->user()->profil->avatar_thumbnail) 
                : (auth()->user()->profil->avatar 
                    ? asset('storage/' . auth()->user()->profil->avatar) 
                    : asset('images/default-avatar.png')) }}" 
                alt="Avatar" 
                style="width: 40px; height: 40px; border-radius: 50%;">
        @else
            <img src="{{ asset('images/default-avatar.png') }}" 
                style="width: 40px; height: 40px; border-radius: 50%;">
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
