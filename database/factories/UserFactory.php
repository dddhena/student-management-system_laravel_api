<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
       return [
            'username' => $this->faker->unique()->userName,
            'password' => function () {
    return Hash::make(match ($this->faker->randomElement(['student', 'teacher', 'admin', 'parent'])) {
        'student' => 'student123',
        'teacher' => 'teacher123',
        'admin' => 'admin123',
        'parent' => 'parent123',
    });
},

            'role' => $this->faker->randomElement(['student', 'teacher', 'admin', 'parent']),
            'language' => $this->faker->randomElement(['Amharic', 'Afaan Oromo', 'English']),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
