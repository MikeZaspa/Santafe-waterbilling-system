<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run()
    {
        // Admin 2
        Admin::create([
            'name' => 'Super Admin', // Change name as needed
            'email' => 'hikoseijaro@gmail.com', // Change email as needed
            'password' => Hash::make('Admin123@'), // Change password as needed
            'email_verified_at' => now(),
            'active' => true,
        ]);
    }
    
}