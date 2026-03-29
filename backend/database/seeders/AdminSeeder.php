<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        // Find the Super Admin role we just created
        $superAdminRole = Role::where('name', 'Super Admin')->first();

        User::create([
            'role_id' => $superAdminRole->id,
            'first_name' => 'System',
            'last_name' => 'Administrator',
            'email' => 'admin@school.com',
            'password' => Hash::make('password123'), // You'll use this to log in
            'status' => 'active',
        ]);
    }
}