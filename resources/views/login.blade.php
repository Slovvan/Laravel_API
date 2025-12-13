@extends('layouts.app')

@section('content')
    {{$errors}}

    <form action="{{ route('login') }}" method="POST">
        @csrf

        <div>
            <label for="email_input">Email</label>
            <input name="email" type="email" id="email_input"/>
        </div>

        <div>
            <label for="password_input">Mot de passe</label>
            <input name="password" type="password" id="password_input"/>
        </div>

        <button type="submit">Connexion</button>
    </form>

@endsection
