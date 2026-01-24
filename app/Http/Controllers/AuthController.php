<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Profils;
use Illuminate\Support\Facades\Hash;
use App\Mail\CommentNotification;
use App\Mail\WelcomeMail;

class AuthController extends Controller
{
    public function getLoginForm(Request $request)
    {
        try {
            $credentials = $request->validate([
                'email' => ['required', 'email'],
                'password' => ['required']
            ]);

            if (Auth::attempt($credentials)) {
                $request->session()->regenerate();
                return redirect(route('welcome'))->with('success', 'Connexion réussie !');
            }

            return back()->withErrors([
                'email' => 'L\'adresse email ou le mot de passe que vous avez saisi est incorrect.'
            ])->onlyInput('email');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Une erreur est survenue lors de la connexion.'])->onlyInput('email');
        }
    }

    public function getRegisterForm(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'email', 'unique:users,email'],
                'password' => ['required', 'confirmed', 'min:8'],
            ]);

            // Créer l'utilisateur
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
            ]);

            // Créer le profil associé
            $profil = Profils::create([
                'user_id' => $user->id,
                'bio' => 'Bienvenue sur mon profil !',
                'avatar' => null
            ]);
            Mail::to($user->email)->queue(new WelcomeMail($user));
            // Connecter l'utilisateur
            Auth::login($user);
            $request->session()->regenerate();

            return redirect(route('welcome'))->with('success', 'Inscription réussie ! Vous êtes maintenant connecté.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Une erreur est survenue lors de l\'inscription: ' . $e->getMessage()])->withInput();
        }
    }

    public function logout(Request $request) 
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('welcome')->with('warning', 'Vous avez été déconnecté.');
    }
}

