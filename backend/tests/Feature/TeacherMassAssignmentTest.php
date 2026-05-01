<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Teacher;
use App\Models\User;

class TeacherMassAssignmentTest extends TestCase
{
    use RefreshDatabase;

    public function test_cannot_mass_assign_id_to_teacher()
    {
        $role = \App\Models\Role::create(['name' => 'Teacher']);
        $user = User::factory()->create(['role_id' => $role->id]);

        $teacherData = [
            'id' => 9999,
            'user_id' => $user->id,
            'employee_number' => 'T-12345',
            'department' => 'Science',
            'hired_date' => '2020-01-01',
        ];

        $teacher = Teacher::create($teacherData);

        // The ID should not be 9999 since mass assignment for 'id' is blocked
        $this->assertNotEquals(9999, $teacher->id);
    }
}
