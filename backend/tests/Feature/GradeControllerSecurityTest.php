<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Grade;
use App\Models\User;
use App\Models\Student;
use App\Models\Assignment;
use App\Models\Section;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\SchoolYear;

class GradeControllerSecurityTest extends TestCase
{
    use RefreshDatabase;

    protected $adminUser;

    protected function setUp(): void
    {
        parent::setUp();

        // Ensure standard DB seed values exist for tests or manually create what is needed
        // Assuming we just need an authenticated user to perform updates.
        // Looking at routes, standard controllers require 'auth:sanctum'.
        $role = \App\Models\Role::create(['name' => 'admin', 'description' => 'Admin Role']);

        $this->adminUser = User::factory()->create(['role_id' => $role->id]);
    }

    public function test_grade_update_rejects_negative_score()
    {
        // First setup prerequisites
        $schoolYear = SchoolYear::create(['label' => '2023-2024', 'start_date' => '2023-08-01', 'end_date' => '2024-06-01']);

        $user = User::factory()->create(['role_id' => 1]);
        $student = Student::create([
            'user_id' => $user->id,
            'student_number' => 'STU12345',
            'gender' => 'Male',
            'birth_date' => '2005-01-01',
            'address' => '123 Main St',
            'guardian_name' => 'John Doe Sr',
            'guardian_contact' => '1234567890',
            'school_year_id' => $schoolYear->id,
        ]);

        $subject = Subject::create([
            'name' => 'Math 101',
            'code' => 'MTH101',
            'description' => 'Basic Math',
        ]);

        $teacherUser = User::factory()->create(['role_id' => 1]);
        $teacher = Teacher::create([
            'user_id' => $teacherUser->id,
            'employee_number' => 'EMP123',
            'department' => 'Math',
            'specialization' => 'Algebra',
            'hired_date' => '2023-01-01',
        ]);

        $section = Section::create([
            'name' => 'Section A',
            'subject_id' => $subject->id,
            'teacher_id' => $teacher->id,
            'school_year_id' => $schoolYear->id,
            'schedule' => 'MWF 9-10AM',
            'room' => 'Room 101',
        ]);

        $assignment = Assignment::create([
            'section_id' => $section->id,
            'title' => 'Homework 1',
            'description' => 'Do it',
            'due_date' => '2024-12-01',
            'max_score' => 100,
        ]);

        $grade = Grade::create([
            'student_id' => $student->id,
            'assignment_id' => $assignment->id,
            'score' => 85,
            'remarks' => 'Good',
        ]);

        // Attempt update with invalid value (e.g. score < 0)
        $response = $this->actingAs($this->adminUser)->putJson("/api/grades/{$grade->id}", [
            'score' => -10, // Invalid value
        ]);

        // Should return validation error for score
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['score']);

        // Assert database was not changed
        $this->assertDatabaseHas('grades', [
            'id' => $grade->id,
            'score' => 85,
        ]);
    }

    public function test_grade_update_prevents_mass_assignment()
    {
        // First setup prerequisites
        $schoolYear = SchoolYear::create(['label' => '2023-2024', 'start_date' => '2023-08-01', 'end_date' => '2024-06-01']);

        $user = User::factory()->create(['role_id' => 1]);
        $student = Student::create([
            'user_id' => $user->id,
            'student_number' => 'STU12345',
            'gender' => 'Male',
            'birth_date' => '2005-01-01',
            'address' => '123 Main St',
            'guardian_name' => 'John Doe Sr',
            'guardian_contact' => '1234567890',
            'school_year_id' => $schoolYear->id,
        ]);

        $subject = Subject::create([
            'name' => 'Math 101',
            'code' => 'MTH101',
            'description' => 'Basic Math',
        ]);

        $teacherUser = User::factory()->create(['role_id' => 1]);
        $teacher = Teacher::create([
            'user_id' => $teacherUser->id,
            'employee_number' => 'EMP123',
            'department' => 'Math',
            'specialization' => 'Algebra',
            'hired_date' => '2023-01-01',
        ]);

        $section = Section::create([
            'name' => 'Section A',
            'subject_id' => $subject->id,
            'teacher_id' => $teacher->id,
            'school_year_id' => $schoolYear->id,
            'schedule' => 'MWF 9-10AM',
            'room' => 'Room 101',
        ]);

        $assignment = Assignment::create([
            'section_id' => $section->id,
            'title' => 'Homework 1',
            'description' => 'Do it',
            'due_date' => '2024-12-01',
            'max_score' => 100,
        ]);

        $grade = Grade::create([
            'student_id' => $student->id,
            'assignment_id' => $assignment->id,
            'score' => 85,
            'remarks' => 'Good',
        ]);

        // Attempt mass assignment with valid score but attempting to change ID
        $response = $this->actingAs($this->adminUser)->putJson("/api/grades/{$grade->id}", [
            'score' => 95,
            'id' => 9999, // Attempting to change ID
        ]);

        $response->assertStatus(200);

        // Assert database was changed for score
        $this->assertDatabaseHas('grades', [
            'id' => $grade->id,
            'score' => 95,
        ]);

        // Assert ID was not changed
        $this->assertDatabaseMissing('grades', [
            'id' => 9999,
        ]);
    }

    public function test_grade_update_succeeds_with_valid_data()
    {
        $schoolYear = SchoolYear::create(['label' => '2023-2024', 'start_date' => '2023-08-01', 'end_date' => '2024-06-01']);

        $user = User::factory()->create(['role_id' => 1]);
        $student = Student::create([
            'user_id' => $user->id,
            'student_number' => 'STU12345',
            'gender' => 'Male',
            'birth_date' => '2005-01-01',
            'address' => '123 Main St',
            'guardian_name' => 'John Doe Sr',
            'guardian_contact' => '1234567890',
            'school_year_id' => $schoolYear->id,
        ]);

        $subject = Subject::create([
            'name' => 'Math 101',
            'code' => 'MTH101',
            'description' => 'Basic Math',
        ]);

        $teacherUser = User::factory()->create(['role_id' => 1]);
        $teacher = Teacher::create([
            'user_id' => $teacherUser->id,
            'employee_number' => 'EMP123',
            'department' => 'Math',
            'specialization' => 'Algebra',
            'hired_date' => '2023-01-01',
        ]);

        $section = Section::create([
            'name' => 'Section A',
            'subject_id' => $subject->id,
            'teacher_id' => $teacher->id,
            'school_year_id' => $schoolYear->id,
            'schedule' => 'MWF 9-10AM',
            'room' => 'Room 101',
        ]);

        $assignment = Assignment::create([
            'section_id' => $section->id,
            'title' => 'Homework 1',
            'description' => 'Do it',
            'due_date' => '2024-12-01',
            'max_score' => 100,
        ]);

        $grade = Grade::create([
            'student_id' => $student->id,
            'assignment_id' => $assignment->id,
            'score' => 85,
            'remarks' => 'Good',
        ]);

        // Valid update
        $response = $this->actingAs($this->adminUser)->putJson("/api/grades/{$grade->id}", [
            'score' => 95,
            'remarks' => 'Excellent',
        ]);

        $response->assertStatus(200);

        // Assert database was changed
        $this->assertDatabaseHas('grades', [
            'id' => $grade->id,
            'score' => 95,
            'remarks' => 'Excellent',
        ]);
    }
}