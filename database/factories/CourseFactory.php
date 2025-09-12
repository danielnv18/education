<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\CourseStatus;
use App\Models\Course;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Course>
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
            'title' => fake()->sentence(3),
            'description' => fake()->paragraph(),
            'status' => fake()->randomElement(CourseStatus::cases()),
            'published_at' => fake()->dateTime(),
            'teacher_id' => User::factory()->create(),
            'start_date' => fake()->dateTimeBetween('now', '+1 month'),
            'end_date' => fake()->dateTimeBetween('+2 months', '+6 months'),
        ];
    }

    /**
     * Indicate that the course is published.
     */
    public function published(): self
    {
        return $this->state(fn (array $attributes): array => [
            'published_at' => now(),
        ]);
    }

    /**
     * Indicate that the course is a draft.
     */
    public function draft(): self
    {
        return $this->state(fn (array $attributes): array => [
            'published_at' => null,
            'status' => CourseStatus::Draft,
        ]);
    }

    /**
     * Indicate that the course is active.
     */
    public function active(): self
    {
        return $this->state(fn (array $attributes): array => [
            'published_at' => now(),
            'status' => CourseStatus::Active,
        ]);
    }

    /**
     * Indicate that the course is archived.
     */
    public function archived(): self
    {
        return $this->state(fn (array $attributes): array => [
            'status' => CourseStatus::Archived,
        ]);
    }

    /**
     * Indicate that the course has no teacher.
     */
    public function withoutTeacher(): self
    {
        return $this->state(fn (array $attributes): array => [
            'teacher_id' => null,
        ]);
    }
}
