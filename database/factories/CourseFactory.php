<?php

declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Course>
 */
final class CourseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(),
            'slug' => fake()->slug(),
            'description' => fake()->paragraph(),
            'status' => fake()->randomElement(['draft', 'published']),
            'teacher_id' => \App\Models\User::factory(),
            'metadata' => [],
            'published_at' => fake()->optional()->dateTime(),
            'starts_at' => fake()->optional()->dateTime(),
            'ends_at' => fake()->optional()->dateTime(),
        ];
    }
}
