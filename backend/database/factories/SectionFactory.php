<?php
namespace Database\Factories;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\SchoolYear;
use App\Models\Subject;
use App\Models\Teacher;
class SectionFactory extends Factory {
    public function definition(): array {
        return [
            'school_year_id' => SchoolYear::factory(),
            'subject_id' => Subject::factory(),
            'teacher_id' => Teacher::factory(),
            'name' => 'Section ' . fake()->word(),
            'room' => 'Room ' . fake()->numberBetween(100, 999),
            'schedule' => 'MWF 9:00AM-10:00AM',
        ];
    }
}