<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\CourseStatus;
use App\Models\File;
use App\Models\User;
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
            'title' => fake()->sentence(3),
            'description' => fake()->paragraph(),
            'status' => fake()->randomElement(CourseStatus::cases()),
            'is_published' => fake()->boolean(),
            'teacher_id' => User::factory(),
            'thumbnail_id' => File::factory(),
            'start_date' => fake()->dateTimeBetween('now', '+1 month'),
            'end_date' => fake()->dateTimeBetween('+2 months', '+6 months'),
        ];
    }

    /**
     * Indicate that the course is published.
     */
    public function published(): CourseFactory
    {
        return $this->state(fn (array $attributes): array => [
            'is_published' => true,
        ]);
    }

    /**
     * Indicate that the course is a draft.
     */
    public function draft(): CourseFactory
    {
        return $this->state(fn (array $attributes): array => [
            'is_published' => false,
            'status' => CourseStatus::DRAFT,
        ]);
    }

    /**
     * Indicate that the course is active.
     */
    public function active(): CourseFactory
    {
        return $this->state(fn (array $attributes): array => [
            'is_published' => true,
            'status' => CourseStatus::ACTIVE,
        ]);
    }

    /**
     * Indicate that the course is archived.
     */
    public function archived(): CourseFactory
    {
        return $this->state(fn (array $attributes): array => [
            'status' => CourseStatus::ARCHIVED,
        ]);
    }

    /**
     * Indicate that the course has no teacher.
     */
    public function withoutTeacher(): CourseFactory
    {
        return $this->state(fn (array $attributes): array => [
            'teacher_id' => null,
        ]);
    }
}
