<?php
namespace Database\Factories;
use Illuminate\Database\Eloquent\Factories\Factory;
class SchoolYearFactory extends Factory {
    public function definition(): array {
        return [
            'label' => '2026-2027',
            'start_date' => now()->startOfYear(),
            'end_date' => now()->endOfYear(),
            'is_active' => true,
        ];
    }
}