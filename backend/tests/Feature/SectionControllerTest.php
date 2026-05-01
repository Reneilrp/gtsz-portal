<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\SchoolYear;
use App\Models\Section;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class SectionControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        // Create a user and authenticate for the API requests
        $this->user = User::factory()->create();
    }

    public function test_index_returns_all_sections_with_relations(): void
    {
        Section::factory()->count(3)->create();

        $response = $this->actingAs($this->user, 'sanctum')->getJson('/api/sections');

        $response->assertStatus(200)
            ->assertJsonCount(3)
            ->assertJsonStructure([
                '*' => [
                    'id',
                    'school_year_id',
                    'subject_id',
                    'teacher_id',
                    'name',
                    'room',
                    'schedule',
                    'school_year' => ['id', 'label'],
                    'subject' => ['id', 'code', 'name'],
                    'teacher' => [
                        'id',
                        'user_id',
                        'employee_number',
                        'user' => ['id', 'first_name', 'last_name']
                    ]
                ]
            ]);
    }

    public function test_store_creates_a_new_section(): void
    {
        $schoolYear = SchoolYear::factory()->create();
        $subject = Subject::factory()->create();
        $teacher = Teacher::factory()->create();

        $payload = [
            'school_year_id' => $schoolYear->id,
            'subject_id' => $subject->id,
            'teacher_id' => $teacher->id,
            'name' => 'Test Section',
            'room' => 'Test Room',
            'schedule' => 'TTh 1:00PM-2:30PM',
        ];

        $response = $this->actingAs($this->user, 'sanctum')->postJson('/api/sections', $payload);

        $response->assertStatus(201)
            ->assertJsonFragment([
                'name' => 'Test Section',
                'room' => 'Test Room',
                'schedule' => 'TTh 1:00PM-2:30PM',
            ]);

        $this->assertDatabaseHas('sections', $payload);
    }

    public function test_store_validates_required_fields(): void
    {
        $response = $this->actingAs($this->user, 'sanctum')->postJson('/api/sections', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['school_year_id', 'subject_id', 'teacher_id', 'name']);
    }

    public function test_show_returns_section_with_all_relations_including_students(): void
    {
        $section = Section::factory()->create();

        // Add some students to the section
        $students = \App\Models\Student::factory()->count(2)->create();
        $section->students()->attach($students->pluck('id'));

        $response = $this->actingAs($this->user, 'sanctum')->getJson("/api/sections/{$section->id}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'id',
                'name',
                'school_year' => ['id'],
                'subject' => ['id'],
                'teacher' => ['id', 'user' => ['id']],
                'students' => [
                    '*' => [
                        'id',
                        'student_number',
                        'user' => ['id', 'first_name', 'last_name']
                    ]
                ]
            ]);

        $this->assertCount(2, $response->json('students'));
    }

    public function test_update_modifies_existing_section(): void
    {
        $section = Section::factory()->create();

        $payload = [
            'name' => 'Updated Section Name',
            'room' => 'Updated Room',
        ];

        $response = $this->actingAs($this->user, 'sanctum')->putJson("/api/sections/{$section->id}", $payload);

        $response->assertStatus(200)
            ->assertJsonFragment($payload);

        $this->assertDatabaseHas('sections', [
            'id' => $section->id,
            'name' => 'Updated Section Name',
            'room' => 'Updated Room',
        ]);
    }

    public function test_destroy_deletes_section(): void
    {
        $section = Section::factory()->create();

        $response = $this->actingAs($this->user, 'sanctum')->deleteJson("/api/sections/{$section->id}");

        $response->assertStatus(200)
            ->assertJson(['message' => 'Section deleted successfully']);

        $this->assertDatabaseMissing('sections', ['id' => $section->id]);
    }
}
