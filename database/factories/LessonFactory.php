<?php

declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Lesson>
 */
final class LessonFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'module_id' => \App\Models\Module::factory(),
            'title' => fake()->sentence(),
            'slug' => fake()->slug(),
            'summary' => fake()->sentence(),
            'order' => fake()->randomNumber(),
            'status' => fake()->randomElement(['draft', 'published']),
            'content_type' => 'markdown',
            'content' => fake()->paragraphs(3, true),
            'duration_minutes' => fake()->numberBetween(5, 60),
            'metadata' => [],
            'published_at' => fake()->optional()->dateTime(),
            'unpublish_at' => fake()->optional()->dateTime(),
        ];
    }
}
