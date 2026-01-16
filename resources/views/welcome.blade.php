@extends('layouts.app')

@section('extra-css')
    <link rel="stylesheet" href="{{ asset('css/welcome.css') }}">
@endsection

@section('content')
    <div class="welcome-hero">
        <div class="welcome-actions">
            @guest
                <a href="{{ route('loginStore') }}" class="btn btn-primary">Se connecter</a>
                <a href="{{ route('registerStore') }}" class="btn btn-secondary">S'inscrire</a>
            @else
                <a href="{{ route('articles.index') }}" class="btn btn-primary">Voir les articles</a>
                <a href="{{ route('articles.create') }}" class="btn btn-secondary">Créer un article</a>
            @endguest
        </div>
    </div>
@endsection

