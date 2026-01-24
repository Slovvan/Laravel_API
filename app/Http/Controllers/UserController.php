<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Profils;
use App\Repositories\UserRepository;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    protected UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }
    public function index(){
        $users = User::all();
        return view('users.index', [
            'users' => $users
        ]);
    }

    public function edit($id){
        $user = $this->userRepository->findById($id);
        $profil = Profils::where('user_id', $id)->first();

        $profil = $user->profil;

        if(!$user){
            return redirect()->route('users.index');
        }

        return view('profil.show', compact('user'))->with('info', 'Vous pouvez modifier les informations de l\'utilisateur.');
    }

    public function update(Request $request, int $id) {
        $user = $this->userRepository->findById($id);

        if (!$user) {
            return redirect()->route('users.index');
        }

        $validated_datas = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)]
        ]);

        $user->update($validated_datas);

        return redirect()->route('users.index')->with('success', 'Les informations de l\'utilisateur ont été mises à jour.');
    }

    public function destroy(int $id)
    {
        $user = $this->userRepository->findById($id);

        if (!$user) {
            return redirect()->route('users.index')->with('error', 'Utilisateur non trouvé.');
        }

        $user->delete();

        return redirect()->route('users.index')->with('success', 'Utilisateur supprimé avec succès.');
    }
}
