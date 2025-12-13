@extends('layouts.app')

@section('content')
    {{$errors->all()}}

    <form action="{{ route('register') }}" method="POST">
        @csrf

        <div>
            <label for="name_input">Nom</label>
            <input name="name" id="name_input" value="{{old('name')}}"/>
        </div>

        <div>
            <label for="email_input">Email</label>
            <input name="email" type="email" id="email_input" value="{{old('email')}}"/>
        </div>

        <div>
            <label for="password_input">Mot de passe</label>
            <input name="password" type="password" id="password_input"/>
        </div>

        <div>
            <label for="password_confirmation">Confirmer le mot de passe :</label>
            <input type="password" id="password_confirmation" name="password_confirmation" required>
        </div>

        <button type="submit">Créer mon compte</button>
    </form>

@endsection