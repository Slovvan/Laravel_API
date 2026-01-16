@extends('layouts.app')

@section('extra-css')
    <link rel="stylesheet" href="{{ asset('css/profile.css') }}">
@endsection

@section('content')
    <div class="edit-form">
        <h1 style="text-align: center; margin-bottom: 2rem; color: #111827;">Modifier mon profil</h1>

        @if($errors->any())
            <div class="form-errors">
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- CORRECCIÓN: action apunta a profil.update y se usa @method('PUT') --}}
        <form action="{{ route('profil.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="form-section">
                <div class="form-section-title">Informations personnelles</div>
                
                <div class="form-group">
                    <label for="name">Nom</label>
                    <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" required>
                </div>

                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" required>
                </div>
            </div>

            <div class="form-section">
                <div class="form-section-title">Sécurité</div>
                <div class="form-group">
                    <label for="password">Nouveau mot de passe</label>
                    <input type="password" id="password" name="password" placeholder="Laisser vide pour ne pas changer">
                </div>
                <div class="form-group">
                    <label for="password_confirmation">Confirmer le mot de passe</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" placeholder="Confirmer le nouveau mot de passe">
                </div>
            </div>

            <div class="form-section">
                <div class="form-section-title">Profil public</div>

                <div class="form-group">
                    <label>Avatar</label>
                    <div id="avatar-preview-container" style="margin-bottom: 1rem;">
                        @if($profil->avatar)
                            <div class="avatar-preview" style="text-align: center;">
                                <img id="avatar-preview-image" src="{{ asset('storage/' . $profil->avatar) }}" style="width: 150px; height: 150px; border-radius: 50%; object-fit: cover; border: 3px solid #007bff;">
                            </div>
                        @else
                            <div id="avatar-placeholder" style="text-align: center; width: 150px; height: 150px; margin: 0 auto; background: #007bff; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-size: 50px;">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>
                        @endif
                    </div>
                    
                    <input type="file" id="avatar" name="avatar" accept="image/*">
                </div>

                <div class="form-group">
                    <label for="bio">Biographie</label>
                    <textarea id="bio" name="bio" rows="5">{{ old('bio', $profil->bio) }}</textarea>
                </div>
            </div>

            <div style="display: flex; gap: 1rem; margin-top: 2rem;">
                {{-- CORRECCIÓN: El botón Annuler ahora redirige al perfil --}}
                <a href="{{ route('profil.show', $profil->id) }}" class="btn btn-outline" style="flex: 1; text-align: center; text-decoration: none; line-height: 2.5; border: 1px solid #ccc; border-radius: 4px;">Annuler</a>
                <button type="submit" class="btn-submit" style="flex: 1; background: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer;">Enregistrer</button>
            </div>
        </form>
    </div>

    <script>
        // Lógica de preview simple
        document.getElementById('avatar').addEventListener('change', function(e) {
            const reader = new FileReader();
            reader.onload = function(event) {
                let previewImg = document.getElementById('avatar-preview-image');
                if (!previewImg) {
                    const container = document.getElementById('avatar-preview-container');
                    container.innerHTML = '<img id="avatar-preview-image" style="width: 150px; height: 150px; border-radius: 50%; object-fit: cover; border: 3px solid #007bff;">';
                    previewImg = document.getElementById('avatar-preview-image');
                }
                previewImg.src = event.target.result;
            };
            reader.readAsDataURL(this.files[0]);
        });
    </script>
@endsection