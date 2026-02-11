<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run()
    {
        Admin::create([
            'name' => 'Admin',
            'email' => 'zaspamike0@gmail.com',
            'password' => Hash::make('Onepiece0507@'), // Change this password
            'email_verified_at' => now(),
            'active' => true,
        ]);

        // Second Admin (New)
        Admin::create([
            'name' => 'Admin', // You can change the name
            'email' => 'hikoseijaro@gmail.com', // Change this email
            'password' => Hash::make('Admin1234@'), // Change this password
            'email_verified_at' => now(),
            'active' => true,
        ]);
    }
}