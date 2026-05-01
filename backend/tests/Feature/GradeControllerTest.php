<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class GradeControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_student_cannot_be_graded_twice_for_same_assignment(): void
    {
        // 1. Create dependencies
        $role = \App\Models\Role::create([
            'name' => 'admin',
            'description' => 'Administrator',
        ]);

        $user = \App\Models\User::factory()->create(['role_id' => $role->id]);

        $schoolYear = \App\Models\SchoolYear::create([
            'label' => '2023-2024',
            'start_date' => '2023-08-01',
            'end_date' => '2024-05-31',
            'is_active' => true,
        ]);

        $studentRole = \App\Models\Role::create([
            'name' => 'student',
            'description' => 'Student',
        ]);
        $studentUser = \App\Models\User::factory()->create(['role_id' => $studentRole->id]);
        $student = \App\Models\Student::create([
            'user_id' => $studentUser->id,
            'student_number' => 'STU-001',
            'gender' => 'Male',
            'birth_date' => '2005-01-01',
            'address' => '123 Main St',
            'guardian_name' => 'John Doe Sr',
            'guardian_contact' => '555-1234',
            'school_year_id' => $schoolYear->id,
        ]);

        $teacherRole = \App\Models\Role::create([
            'name' => 'teacher',
            'description' => 'Teacher',
        ]);
        $teacherUser = \App\Models\User::factory()->create(['role_id' => $teacherRole->id]);
        $teacher = \App\Models\Teacher::create([
            'user_id' => $teacherUser->id,
            'employee_number' => 'EMP-001',
            'department' => 'Science',
            'specialization' => 'Physics',
            'hired_date' => '2020-01-01',
        ]);

        $subject = \App\Models\Subject::create([
            'code' => 'PHY101',
            'name' => 'Basic Physics',
        ]);

        $section = \App\Models\Section::create([
            'school_year_id' => $schoolYear->id,
            'subject_id' => $subject->id,
            'teacher_id' => $teacher->id,
            'name' => 'Block A',
        ]);

        $assignment = \App\Models\Assignment::create([
            'section_id' => $section->id,
            'title' => 'Midterm Exam',
            'max_score' => 100,
            'due_date' => '2023-10-15 12:00:00',
        ]);

        $payload = [
            'student_id' => $student->id,
            'assignment_id' => $assignment->id,
            'score' => 95.5,
            'remarks' => 'Good job',
        ];

        // 2. First POST request should succeed
        $response1 = $this->actingAs($user)->postJson('/api/grades', $payload);
        $response1->assertStatus(201);

        // 3. Second POST request with same student and assignment should fail
        $response2 = $this->actingAs($user)->postJson('/api/grades', $payload);
        $response2->assertStatus(422)
                 ->assertJsonPath('message', 'Grade already exists for this assignment');
    }
}
