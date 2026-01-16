@extends('layouts.auth')

@section('title', 'Inscription')

@section('content')
    <div class="auth-header">
        <h1>Créer un compte</h1>
        <p>Rejoignez notre communauté</p>
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

    <form action="{{ route('registerStore') }}" method="POST" class="auth-form">
        @csrf

        <div class="form-group">
            <label for="name_input">Nom complet</label>
            <input 
                type="text"
                id="name_input" 
                name="name"
                placeholder="Jean Dupont"
                value="{{ old('name') }}"
                required
                class="@error('name') error @enderror"
            />
            @error('name')
                <small class="error-text">{{ $message }}</small>
            @enderror
        </div>

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
            <small class="form-text">Minimum 8 caractères</small>
        </div>

        <div class="form-group">
            <label for="password_confirmation">Confirmer le mot de passe</label>
            <input 
                type="password" 
                id="password_confirmation" 
                name="password_confirmation"
                placeholder="••••••••"
                required
            />
        </div>

        <button type="submit" class="btn-submit">Créer mon compte</button>
    </form>

    <div class="auth-link">
        Vous avez déjà un compte ? <a href="{{ route('loginStore') }}">Se connecter</a>
    </div>
@endsection

