<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            ['name' => 'Super Admin', 'description' => 'Full system control'],
            ['name' => 'Admin', 'description' => 'Manages people and data'],
            ['name' => 'Teacher', 'description' => 'Class and student view'],
            ['name' => 'Student', 'description' => 'Learner portal view'],
        ];

        foreach ($roles as $role) {
            Role::create($role);
        }
    }
}