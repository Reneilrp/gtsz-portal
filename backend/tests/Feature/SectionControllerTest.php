<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Section;
use App\Models\SchoolYear;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\User;

class SectionControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_update_validates_mass_assignment()
    {
        // Arrange: Create necessary relationships
        $user = User::factory()->create();
        $schoolYear = SchoolYear::create([
            'label' => '2023-2024',
            'start_date' => '2023-08-01',
            'end_date' => '2024-05-31',
            'is_active' => true
        ]);
        $subject = Subject::create(['name' => 'Math', 'code' => 'MATH101']);
        $teacher = Teacher::create([
            'user_id' => $user->id,
            'employee_number' => 'EMP001',
            'department' => 'Mathematics',
            'hired_date' => '2023-01-01'
        ]);

        $section = Section::create([
            'school_year_id' => $schoolYear->id,
            'subject_id' => $subject->id,
            'teacher_id' => $teacher->id,
            'name' => 'Section A',
            'room' => '101',
            'schedule' => 'Mon-Wed 8AM',
        ]);

        $this->actingAs($user);

        // Attempt to mass assign unvalidated attributes
        $response = $this->putJson("/api/sections/{$section->id}", [
            'name' => 'Section B',
            'unvalidated_attribute' => 'Should be ignored',
        ]);

        $response->assertStatus(200)
                 ->assertJsonFragment(['name' => 'Section B']);

        // Assert the unvalidated attribute isn't magically added or something
        $this->assertDatabaseHas('sections', [
            'id' => $section->id,
            'name' => 'Section B',
        ]);

        // Ensure invalid foreign key is rejected
        $response = $this->putJson("/api/sections/{$section->id}", [
            'school_year_id' => 9999, // Invalid ID
        ]);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['school_year_id']);
    }
}
