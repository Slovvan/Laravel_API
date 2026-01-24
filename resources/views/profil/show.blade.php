@extends('layouts.app')

@section('extra-css')
    <link rel="stylesheet" href="{{ asset('css/profile.css') }}">
    <style>
        .profile-avatar-image {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #99a0c0;
            box-shadow: none;
        }
        .profile-avatar-placeholder {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            background: #f2f2f2;
            color: #8a1f11;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 60px;
            font-weight: bold;
            border: 2px solid #99a0c0;
            box-shadow: none;
        }
    </style>
@endsection

@section('content')
    <div style="max-width: 600px; margin: 0 auto;">
        <div class="profile-card">
            <div class="profile-header" style="text-align: center; padding: 20px;">
                @if($profil->avatar_thumbnail && file_exists(public_path('storage/' . $profil->avatar_thumbnail)))
                    <img src="{{ asset('storage/' . $profil->avatar_thumbnail) }}" 
                        alt="Avatar de {{ $profil->user->name }}" 
                        class="profile-avatar-image">
                @elseif($profil->avatar && file_exists(public_path('storage/' . $profil->avatar)))
                    <img src="{{ asset('storage/' . $profil->avatar) }}" 
                        alt="Avatar de {{ $profil->user->name }}" 
                        class="profile-avatar-image">
                @else
                    <div class="profile-avatar-placeholder">
                        {{ strtoupper(substr($profil->user->name, 0, 1)) }}
                    </div>
                @endif
                
                <h2 class="profile-name" style="margin-top: 15px;">{{ $profil->user->name }}</h2>
                <p class="profile-email">{{ $profil->user->email }}</p>
            </div>

            @if($profil->bio)
                <div class="profile-bio">
                    {{ $profil->bio }}
                </div>
            @endif

            <div class="profile-body">
                <div class="profile-section">
                    <div class="profile-section-title">Informations</div>
                    <div class="profile-info">
                        <div class="profile-info-item">
                            <div class="profile-info-label">Email</div>
                            <div class="profile-info-value">{{ $profil->user->email }}</div>
                        </div>
                        <div class="profile-info-item">
                            <div class="profile-info-label">Membre depuis</div>
                            <div class="profile-info-value">{{ $profil->created_at->format('d/m/Y') }}</div>
                        </div>
                    </div>
                </div>

                @if(auth()->id() === $profil->user_id)
                    <div style="text-align: center; margin-top: 2rem;">
                        <a href="{{ route('profil.edit') }}" class="btn btn-primary">
                            Modifier mon profil
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
