@extends('layouts.app')

@section('content')
    <h1>Modification de {{$user->name}}</h1>

    <form action="{{route('users.update', ['id' => $user->id])}}" method="POST">
        @csrf

        <div>
            <label for="name_input">Nom</label>
            <input name="name" id="name_input" value="{{old('name', $user->name)}}"/>
        </div>

        <div>
            <label for="email_input">Email</label>
            <input name="email" type="email" id="email_input" value="{{old('email', $user->email)}}"/>
        </div>

         <form action="{{ route('profil.update', $profil->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div>
            <label for="bio">Bio:</label>
            <textarea id="bio" name="bio" required>{{ old('bio', $profil->bio) }}</textarea>
        </div>

        <button type="submit">mise bio a jour</button>
        <a href="{{ route('welcome') }}">Canceler</a>
    </form>

        <button>Modifier</button>
    </form>
@endsection