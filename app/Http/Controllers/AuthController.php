<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Profils;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function getLoginForm(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required']
        ]);

        if (Auth::attempt($credentials)) {
            return redirect(route('welcome'))->with('success', 'Connexion réussie !');;
        }

        return back()->withErrors([
            'email' => 'L\'adresse email ou le mot de passe que vous avez saisi est incorrect.'
        ]);
    }

    public function getRegisterForm(Request $request)
    {
        $credentials = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'confirmed', 'min:8'],
        ]);
        //Créer un utilisateur ici
        
        //Connexion automatique au nouveau c
        #Auth::login($user); is for using the user's data on pages right after registering
        #Auth::attempt($credentials); only for logging in with the given credentials

        $user = User::create([
            'name' => $credentials['name'],
            'email' => $credentials['email'],
            'password' => Hash::make($credentials['password']),
        ]);

        $profil = Profils::create([
            'user_id' => $user->id,
            'bio' => 'Empty',
            'avatar' => 'Empty'
        ]);
        
        Auth::Login($user);
        return redirect(route('welcome'))->with('success', 'Inscription réussie ! Vous êtes maintenant connecté.');
    }


    public function logout(Request $request) {
        Auth::logout();
        return redirect()->route('welcome')->with('warning', 'Vous avez été déconnecté.');
    }
}
