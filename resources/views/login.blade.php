@extends('layouts.auth')

@section('title', 'Connexion')

@section('content')
    <div class="auth-header">
        <h1>Connexion</h1>
        <p>Accédez à votre compte</p>
    </div>

    @if($errors->any())
        <div class="errors">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('loginStore') }}" method="POST" class="auth-form">
        @csrf

        <div class="form-group">
            <label for="email_input">Adresse Email</label>
            <input 
                type="email" 
                id="email_input" 
                name="email"
                placeholder="vous@example.com"
                value="{{ old('email') }}"
                required
                class="@error('email') error @enderror"
            />
            @error('email')
                <small class="error-text">{{ $message }}</small>
            @enderror
        </div>

        <div class="form-group">
            <label for="password_input">Mot de passe</label>
            <input 
                type="password" 
                id="password_input" 
                name="password"
                placeholder="••••••••"
                required
                class="@error('password') error @enderror"
            />
            @error('password')
                <small class="error-text">{{ $message }}</small>
            @enderror
        </div>

        <button type="submit" class="btn-submit">Se connecter</button>
    </form>

    <div class="auth-link">
        Pas de compte ? <a href="{{ route('registerStore') }}">Créer un compte</a>
    </div>
@endsection
