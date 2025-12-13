<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Profils;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;

class ProfilController extends Controller
{
    public function show($profil)
    {
        if (is_numeric($profil)) {
            $profil = Profils::find($profil);
        } else {
            $profil = Profils::where('id', $profil)->first();
        }

        if(!$profil){
            return redirect()->route('welcome')->with('error', 'Profil non trouvé.');
        }

        return view('profil.show', compact('profil'));
    }

    public function edit()
    {
        $user = auth()->user();
        $profil = $user->profil;

        if (!$profil) {
            // Si por alguna razón no existe el perfil, lo creamos
            $profil = Profils::create([
                'user_id' => $user->id,
                'bio' => 'Bienvenue sur mon profil !',
                'avatar' => null,
            ]);
        }

        return view('profil.edit', compact('user', 'profil'));
    }

    public function update(Request $request)
    {
        $user = auth()->user();
        $profil = $user->profil;

        // Validación de todos los campos
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email,' . $user->id],
            'bio' => ['nullable', 'string', 'max:500'],
            'avatar' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
            'password' => ['nullable', 'confirmed', 'min:8'],
        ]);

        // Actualizar datos del usuario
        $user->name = $validated['name'];
        $user->email = $validated['email'];

        // Si se proporciona una nueva contraseña, actualizarla
        if ($request->filled('password')) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        // Actualizar bio del perfil
        if ($request->filled('bio')) {
            $profil->bio = $validated['bio'];
        }

        // Manejar la subida del avatar
        if ($request->hasFile('avatar')) {
            // Eliminar el avatar anterior si existe
            if ($profil->avatar && Storage::disk('public')->exists($profil->avatar)) {
                Storage::disk('public')->delete($profil->avatar);
            }

            // Guardar el nuevo avatar
            $avatarPath = $request->file('avatar')->store('avatars', 'public');
            $profil->avatar = $avatarPath;
        }

        $profil->save();

        return redirect()->route('profil.edit')->with('success', 'Votre profil a été mis à jour avec succès.');
    }
}