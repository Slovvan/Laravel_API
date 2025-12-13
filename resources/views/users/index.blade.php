@extends('layouts.app')

@section('content')

    <table>
        <thead>
        <tr>
            <th>Nom</th>
            <th>Email</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        @foreach($users as $user)
            <tr>
                <td>
                    {{$user->name}}
                </td>
                <td>
                    {{$user->email}}
                </td>
                <td>
                    <button>
                        <a href="{{route('users.edit', ['id' => $user->id])}}">
                            Éditer
                        </a>
                    </button>
                    <button>
                        @if(Auth::id() != $user->id)
                            <form action="{{ route('users.destroy', $user->id) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button>Supprimer</button>
                            </form>
                        @endif
                    </button>
                </td>
            </tr>
        @endforeach

        </tbody>
    </table>
@endsection