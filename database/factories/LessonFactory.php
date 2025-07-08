<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\LessonType;
use App\Models\Module;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\Lesson>
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
            'title' => fake()->sentence(3),
            'content' => fake()->paragraphs(3, true),
            'module_id' => Module::factory(),
            'order' => fake()->numberBetween(1, 100),
            'type' => fake()->randomElement(LessonType::cases()),
            'is_published' => fake()->boolean(),
        ];
    }

    /**
     * Indicate that the lesson is published.
     */
    public function published(): self
    {
        return $this->state(fn (array $attributes): array => [
            'is_published' => true,
        ]);
    }

    /**
     * Indicate that the lesson is unpublished.
     */
    public function unpublished(): self
    {
        return $this->state(fn (array $attributes): array => [
            'is_published' => false,
        ]);
    }

    /**
     * Set a specific order for the lesson.
     */
    public function withOrder(int $order): self
    {
        return $this->state(fn (array $attributes): array => [
            'order' => $order,
        ]);
    }

    /**
     * Set the lesson type to text.
     */
    public function asText(): self
    {
        return $this->state(fn (array $attributes): array => [
            'type' => LessonType::TEXT,
        ]);
    }

    /**
     * Set the lesson type to video.
     */
    public function asVideo(): self
    {
        return $this->state(fn (array $attributes): array => [
            'type' => LessonType::VIDEO,
        ]);
    }

    /**
     * Set the lesson type to document.
     */
    public function asDocument(): self
    {
        return $this->state(fn (array $attributes): array => [
            'type' => LessonType::DOCUMENT,
        ]);
    }
}
