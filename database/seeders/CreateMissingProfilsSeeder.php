<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Profils;
use Illuminate\Database\Seeder;

class CreateMissingProfilsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtener todos los usuarios que no tienen perfil
        $usersWithoutProfil = User::doesntHave('profil')->get();

        foreach ($usersWithoutProfil as $user) {
            Profils::create([
                'user_id' => $user->id,
                'bio' => 'Bienvenue sur mon profil !',
                'avatar' => null,
            ]);
        }

        $this->command->info('✓ ' . count($usersWithoutProfil) . ' profils ont été créés pour les utilisateurs qui n\'en avaient pas.');
    }
}
