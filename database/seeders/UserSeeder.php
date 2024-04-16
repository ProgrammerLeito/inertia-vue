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
        User::create([
            'name' => 'Leonardo',
            'email' => 'leonardo@balinsa.net',
            'password' => Hash::make('Leonardo150724#.'),
            'sexo' => 'Masculino',
            'celular' => 931311244,
        ]);

        User::create([
            'name' => 'Ingri',
            'email' => 'ingri@balinsa.net',
            'password' => Hash::make('ingri2024#.'),
            'sexo' => 'Femenino',
            'celular' => 99999999,
        ]);


        User::create([
            'name' => 'prueba',
            'email' => 'prueba@balinsa.net',
            'password' => Hash::make('prueba2024'),
            'sexo' => 'Masculino',
            'celular' => 999999999,
        ]);
    }
}
