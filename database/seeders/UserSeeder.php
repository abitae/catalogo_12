<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear usuario administrador
        $user = User::factory()->create([
            'name' => 'Abel Arana Cortez',
            'email' => 'abel.arana@hotmail.com',
            'password' => Hash::make('lobomalo123'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $user->assignRole('Super Admin');
    }
}
