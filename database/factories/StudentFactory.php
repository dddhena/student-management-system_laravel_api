<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\ParentModel;
use App\Models\User;
/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Student>
 */
class StudentFactory extends Factory
{
     public function definition(): array
    {
        return [
        'full_name' => $this->faker->name,
        'gender' => $this->faker->randomElement(['Male', 'Female']),
        'date_of_birth' => $this->faker->date(),
        'grade' => $this->faker->randomElement(['Grade 1', 'Grade 2', 'Grade 3']),
        'section' => $this->faker->randomElement(['A', 'B', 'C']),
        'user_id' => User::factory(), // creates a unique user for each student
        'guardian_id' => ParentModel::inRandomOrder()->first()->id ?? null,
    ];
    }
}