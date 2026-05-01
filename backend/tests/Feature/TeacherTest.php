<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Teacher;
use App\Models\User;
use App\Models\Role;

class TeacherTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Ensure roles exist
        $role = Role::firstOrCreate(['name' => 'teacher']);

        $this->user = User::create([
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'teacher@example.com',
            'password' => bcrypt('password'),
            'role_id' => $role->id,
        ]);

        $this->teacher = Teacher::create([
            'user_id' => $this->user->id,
            'employee_number' => 'T-12345',
            'department' => 'Science',
            'specialization' => 'Physics',
            'hired_date' => '2020-01-01',
        ]);
    }

    public function test_teacher_update_prevents_mass_assignment()
    {
        // Try to update the teacher's department, and also try to inject a non-fillable or sensitive field
        // E.g. 'is_admin' or trying to hijack 'user_id' with an invalid value
        $response = $this->actingAs($this->user)->putJson('/api/teachers/' . $this->teacher->id, [
            'department' => 'Math',
            'is_admin' => true, // Malicious field
        ]);

        $response->assertStatus(200);

        // Verify the allowed field was updated
        $this->assertDatabaseHas('teachers', [
            'id' => $this->teacher->id,
            'department' => 'Math',
        ]);

        // Attempting to update a field that does not exist on the model normally causes SQL error if passed directly to update()
        // Or if it did exist and wasn't validated, it might update.
        // Because we now use $request->validate(), 'is_admin' is completely ignored.
        // If we try to update a validated field with an invalid value, it should fail validation.
        $responseInvalid = $this->actingAs($this->user)->putJson('/api/teachers/' . $this->teacher->id, [
            'user_id' => 99999, // Assuming this user ID does not exist
        ]);

        $responseInvalid->assertStatus(422); // Validation error
    }
}
