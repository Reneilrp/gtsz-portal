<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Role;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\SchoolYear;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed roles since they are needed for registration
        Role::firstOrCreate(['name' => 'Student', 'description' => 'Student Role']);
        Role::firstOrCreate(['name' => 'Teacher', 'description' => 'Teacher Role']);
        Role::firstOrCreate(['name' => 'Admin', 'description' => 'Admin Role']);
    }

    public function test_can_register_student_successfully()
    {
        $schoolYear = SchoolYear::create([
            'label' => '2023-2024',
            'start_date' => '2023-08-01',
            'end_date' => '2024-05-31',
            'is_active' => true,
        ]);

        $payload = [
            'role' => 'Student',
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john.doe@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'student_number' => 'STU-12345',
            'birth_date' => '2005-05-15',
            'gender' => 'Male',
            'address' => '123 Main St',
            'guardian_name' => 'Jane Doe',
            'guardian_contact' => '09123456789',
            'school_year_id' => $schoolYear->id,
        ];

        $response = $this->postJson('/api/register', $payload);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'user' => [
                    'id', 'first_name', 'last_name', 'email', 'status', 'role'
                ],
                'token',
                'message'
            ])
            ->assertJsonPath('message', 'Registration successful.');

        $this->assertDatabaseHas('users', [
            'email' => 'john.doe@example.com',
            'first_name' => 'John',
            'status' => 'active',
        ]);

        $this->assertDatabaseHas('students', [
            'student_number' => 'STU-12345',
            'guardian_name' => 'Jane Doe',
        ]);
    }

    public function test_can_register_teacher_successfully()
    {
        $payload = [
            'role' => 'Teacher',
            'first_name' => 'Jane',
            'last_name' => 'Smith',
            'email' => 'jane.smith@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'employee_number' => 'EMP-98765',
            'department' => 'Science',
            'hired_date' => '2020-01-10',
        ];

        $response = $this->postJson('/api/register', $payload);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'user' => [
                    'id', 'first_name', 'last_name', 'email', 'status', 'role'
                ],
                'token',
                'message'
            ])
            ->assertJsonPath('message', 'Registration successful.');

        $this->assertDatabaseHas('users', [
            'email' => 'jane.smith@example.com',
            'first_name' => 'Jane',
        ]);

        $this->assertDatabaseHas('teachers', [
            'employee_number' => 'EMP-98765',
            'department' => 'Science',
        ]);
    }

    public function test_registration_fails_due_to_missing_fields()
    {
        $response = $this->postJson('/api/register', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['role', 'first_name', 'last_name', 'email', 'password']);
    }

    public function test_registration_fails_due_to_invalid_role()
    {
        $payload = [
            'role' => 'InvalidRole',
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        $response = $this->postJson('/api/register', $payload);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['role']);
    }

    public function test_can_login_successfully()
    {
        $role = Role::where('name', 'Student')->first();

        $user = User::create([
            'role_id' => $role->id,
            'first_name' => 'Test',
            'last_name' => 'User',
            'email' => 'test@example.com',
            'password' => bcrypt('password123'),
            'status' => 'active',
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'user',
                'token'
            ]);
    }

    public function test_login_fails_with_incorrect_password()
    {
        $role = Role::where('name', 'Student')->first();

        $user = User::create([
            'role_id' => $role->id,
            'first_name' => 'Test',
            'last_name' => 'User',
            'email' => 'test@example.com',
            'password' => bcrypt('password123'),
            'status' => 'active',
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'test@example.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    public function test_login_fails_for_inactive_user()
    {
        $role = Role::where('name', 'Student')->first();

        $user = User::create([
            'role_id' => $role->id,
            'first_name' => 'Test',
            'last_name' => 'User',
            'email' => 'test@example.com',
            'password' => bcrypt('password123'),
            'status' => 'inactive',
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(403)
            ->assertJsonPath('message', 'Your account is disabled.');
    }

    public function test_can_logout_successfully()
    {
        $role = Role::where('name', 'Student')->first();

        $user = User::create([
            'role_id' => $role->id,
            'first_name' => 'Test',
            'last_name' => 'User',
            'email' => 'test@example.com',
            'password' => bcrypt('password123'),
            'status' => 'active',
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/logout');

        $response->assertStatus(200)
            ->assertJsonPath('message', 'Logged out successfully.');

        $this->assertDatabaseMissing('personal_access_tokens', [
            'tokenable_id' => $user->id,
            'name' => 'auth_token',
        ]);
    }

    public function test_logout_fails_for_unauthenticated_user()
    {
        $response = $this->postJson('/api/logout');

        $response->assertStatus(401);
    }

    public function test_can_get_authenticated_user_profile()
    {
        $role = Role::where('name', 'Student')->first();

        $user = User::create([
            'role_id' => $role->id,
            'first_name' => 'Test',
            'last_name' => 'User',
            'email' => 'test@example.com',
            'password' => bcrypt('password123'),
            'status' => 'active',
        ]);

        $schoolYear = SchoolYear::create([
            'label' => '2023-2024',
            'start_date' => '2023-08-01',
            'end_date' => '2024-05-31',
            'is_active' => true,
        ]);

        Student::create([
            'user_id' => $user->id,
            'student_number' => 'STU-99999',
            'birth_date' => '2005-01-01',
            'gender' => 'Male',
            'address' => '123 Main St',
            'guardian_name' => 'Jane Doe',
            'guardian_contact' => '09123456789',
            'school_year_id' => $schoolYear->id,
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/user');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'user' => [
                    'id', 'first_name', 'last_name', 'email', 'status', 'role', 'student', 'teacher'
                ]
            ]);
    }

    public function test_get_profile_fails_for_unauthenticated_user()
    {
        $response = $this->getJson('/api/user');

        $response->assertStatus(401);
    }
}
