@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-body">
            @if($profil->avatar)
                <img src="{{ asset('storage/' . $profil->avatar) }}" 
                        alt="Avatar de {{ $profil->user->name }}" 
                        class="rounded-circle mb-3"
                        style="width: 150px; height: 150px; object-fit: cover;">
            @else
                <div class="rounded-circle bg-secondary d-inline-flex align-items-center justify-content-center mb-3" 
                        style="width: 150px; height: 150px;">
                    <span class="fs-1 text-white">{{ substr($profil->user->name, 0, 1) }}</span>
                </div>
            @endif
            
            <h2>{{ $profil->user->name }}</h2>
            <p class="text-muted">{{ $profil->user->email }}</p>
            
            @if($profil->bio)
                <hr>
                <p>{{ $profil->bio }}</p>
            @endif
            
            @if(auth()->id() === $profil->user_id)
                <a href="{{ route('profil.edit') }}" class="btn btn-primary mt-3">
                    Modifier mon profil
                </a>
            @endif
        </div>
    </div>
</div>
@endsection