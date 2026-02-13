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
            'email' => 'hikoseijaro@gmail.com',
            'password' => Hash::make('Onepiece0507@'), // Change this password
            'email_verified_at' => now(),
            'active' => true,
        ]);
    }
}