<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\Profils;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::where('email', 'admin@gmail.com')->first();

        if (!$admin) {
            // Crear el usuario admin
            $admin = User::create([
                'name' => 'Admin',
                'email' => 'admin@gmail.com',
                'is_admin' => 'admin',
                'password' => Hash::make('password'),
            ]);

            Profils::create([
                'user_id' => $admin->id,
                'bio' => 'Administrateur du système',
                'avatar' => null,
            ]);

            $this->command->info('Admin cré!');
        } else {
            $this->command->warn('Déjà existe.');
        }
    }
}
