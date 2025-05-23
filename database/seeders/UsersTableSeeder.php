<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    public function run()
    {
        // Usuario cliente
        User::create([
            'name' => 'Cliente Demo',
            'email' => 'cliente@example.com',
            'password' => Hash::make('password'),
            'role' => 'cliente',
        ]);
        
        // Usuario vendedor
        User::create([
            'name' => 'Vendedor Demo',
            'email' => 'vendedor@example.com',
            'password' => Hash::make('password'),
            'role' => 'vendedor',
        ]);
        
        // Usuario proveedor
        User::create([
            'name' => 'Proveedor Demo',
            'email' => 'proveedor@example.com',
            'password' => Hash::make('password'),
            'role' => 'proveedor',
        ]);
    }
}
