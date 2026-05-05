<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run()
    {
        User::create([
            'name' => 'Admin System',
            'phone' => '0987654321',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
            'strikes' => 0,
            'is_banned' => false,
        ]);
    }
}