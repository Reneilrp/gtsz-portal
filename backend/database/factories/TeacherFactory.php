<?php
namespace Database\Factories;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
class TeacherFactory extends Factory {
    public function definition(): array {
        return [
            'user_id' => User::factory(),
            'employee_number' => fake()->unique()->numerify('T-#####'),
            'department' => fake()->word(),
            'specialization' => fake()->word(),
            'hired_date' => fake()->date(),
        ];
    }
}