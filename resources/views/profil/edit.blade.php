@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header">
            <h2>Modifier mon profil</h2>
        </div>
        <div class="card-body">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('profil.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <!-- Sección de información del usuario -->
                <h4 class="mb-3">Informations personnelles</h4>
                
                <div class="mb-3">
                    <label for="name" class="form-label">Nom</label>
                    <input 
                        type="text" 
                        class="form-control @error('name') is-invalid @enderror" 
                        id="name" 
                        name="name" 
                        value="{{ old('name', $user->name) }}" 
                        required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input 
                        type="email" 
                        class="form-control @error('email') is-invalid @enderror" 
                        id="email" 
                        name="email" 
                        value="{{ old('email', $user->email) }}" 
                        required>
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <hr class="my-4">

                <!-- Sección de cambio de contraseña -->
                <h4 class="mb-3">Changer le mot de passe (optionnel)</h4>
                
                <div class="mb-3">
                    <label for="password" class="form-label">Nouveau mot de passe</label>
                    <input 
                        type="password" 
                        class="form-control @error('password') is-invalid @enderror" 
                        id="password" 
                        name="password"
                        placeholder="Laisser vide pour ne pas changer">
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="form-text text-muted">Minimum 8 caractères</small>
                </div>

                <div class="mb-3">
                    <label for="password_confirmation" class="form-label">Confirmer le mot de passe</label>
                    <input 
                        type="password" 
                        class="form-control" 
                        id="password_confirmation" 
                        name="password_confirmation"
                        placeholder="Confirmer le nouveau mot de passe">
                </div>

                <hr class="my-4">

                <!-- Sección de perfil -->
                <h4 class="mb-3">Profil public</h4>

                <div class="mb-3">
                    <label for="avatar" class="form-label">Avatar</label>
                    
                    @if($profil->avatar)
                        <div class="mb-2">
                            <img src="{{ asset('storage/' . $profil->avatar) }}" 
                                alt="Avatar actuel" 
                                class="img-thumbnail"
                                style="max-width: 150px;">
                        </div>
                    @endif
                    
                    <input 
                        type="file" 
                        class="form-control @error('avatar') is-invalid @enderror" 
                        id="avatar" 
                        name="avatar"
                        accept="image/jpeg,image/png,image/jpg,image/gif">
                    @error('avatar')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="form-text text-muted">Format: JPG, PNG, GIF. Taille max: 2MB</small>
                </div>

                <div class="mb-3">
                    <label for="bio" class="form-label">Bio</label>
                    <textarea 
                        class="form-control @error('bio') is-invalid @enderror" 
                        id="bio" 
                        name="bio" 
                        rows="5"
                        placeholder="Parlez-nous de vous...">{{ old('bio', $profil->bio) }}</textarea>
                    @error('bio')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="form-text text-muted">Maximum 500 caractères</small>
                </div>

                <div class="d-flex justify-content-between">
                    <a href="{{ route('welcome') }}" class="btn btn-secondary">Annuler</a>
                    <button type="submit" class="btn btn-primary">Enregistrer les modifications</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection