<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::create([
            'name' => 'Leonardo',
            'email' => 'leonardo@balinsa.net',
            'password' => Hash::make('Leonardo150724#.')
        ]);

        $admin->assignRole('admin');

        $editor = User::create([
            'name' => 'Ingri',
            'email' => 'ingri@balinsa.net',
            'password' => Hash::make('ingri2024#.')
        ]);

        $editor->assignRole('editor');

        $editor = User::create([
            'name' => 'prueba',
            'email' => 'prueba@balinsa.net',
            'password' => Hash::make('prueba2024')
        ]);

        $editor->assignRole('editor');
    }
}
