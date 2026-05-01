<?php

namespace Tests\Feature;

use App\Models\Subject;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class SubjectControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();

        // Authenticate a user for the protected routes
        Sanctum::actingAs(
            User::factory()->create(),
            ['*']
        );
    }

    public function test_can_list_all_subjects(): void
    {
        Subject::factory()->count(3)->create();

        $response = $this->getJson('/api/subjects');

        $response->assertStatus(200)
            ->assertJsonCount(3);
    }

    public function test_can_create_a_subject(): void
    {
        $data = [
            'name' => 'Computer Science',
            'code' => 'CS101',
            'description' => 'Intro to CS',
        ];

        $response = $this->postJson('/api/subjects', $data);

        $response->assertStatus(201)
            ->assertJsonFragment([
                'name' => 'Computer Science',
                'code' => 'CS101',
            ]);

        $this->assertDatabaseHas('subjects', [
            'code' => 'CS101',
        ]);
    }

    public function test_cannot_create_subject_with_invalid_data(): void
    {
        $data = [
            'name' => '', // Required
            'code' => 'CS101',
        ];

        $response = $this->postJson('/api/subjects', $data);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name']);
    }

    public function test_cannot_create_subject_with_duplicate_code(): void
    {
        Subject::factory()->create([
            'code' => 'CS101',
        ]);

        $data = [
            'name' => 'Another Subject',
            'code' => 'CS101', // Duplicate code
        ];

        $response = $this->postJson('/api/subjects', $data);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['code']);
    }

    public function test_can_show_a_subject(): void
    {
        $subject = Subject::factory()->create([
            'name' => 'Mathematics',
            'code' => 'MATH101',
        ]);

        $response = $this->getJson("/api/subjects/{$subject->id}");

        $response->assertStatus(200)
            ->assertJsonFragment([
                'name' => 'Mathematics',
                'code' => 'MATH101',
            ]);
    }

    public function test_returns_404_if_subject_not_found(): void
    {
        $response = $this->getJson('/api/subjects/999');

        $response->assertStatus(404);
    }

    public function test_can_update_a_subject(): void
    {
        $subject = Subject::factory()->create([
            'name' => 'Old Name',
            'code' => 'OLD101',
        ]);

        $data = [
            'name' => 'New Name',
        ];

        $response = $this->putJson("/api/subjects/{$subject->id}", $data);

        $response->assertStatus(200)
            ->assertJsonFragment([
                'name' => 'New Name',
                'code' => 'OLD101',
            ]);

        $this->assertDatabaseHas('subjects', [
            'id' => $subject->id,
            'name' => 'New Name',
        ]);
    }

    public function test_can_delete_a_subject(): void
    {
        $subject = Subject::factory()->create();

        $response = $this->deleteJson("/api/subjects/{$subject->id}");

        $response->assertStatus(200)
            ->assertJson(['message' => 'Subject deleted successfully']);

        $this->assertDatabaseMissing('subjects', [
            'id' => $subject->id,
        ]);
    }
}
