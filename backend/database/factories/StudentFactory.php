<?php
namespace Database\Factories;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
use App\Models\SchoolYear;
class StudentFactory extends Factory {
    public function definition(): array {
        return [
            'user_id' => User::factory(),
            'student_number' => fake()->unique()->numerify('S-#####'),
            'gender' => fake()->randomElement(['Male', 'Female']),
            'birth_date' => fake()->date(),
            'address' => fake()->address(),
            'guardian_name' => fake()->name(),
            'guardian_contact' => fake()->phoneNumber(),
            'school_year_id' => SchoolYear::factory(),
            'enrolled_at' => now(),
        ];
    }
}