<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Course;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Module>
 */
final class ModuleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(2),
            'description' => fake()->paragraph(),
            'course_id' => Course::factory(),
            'order' => fake()->numberBetween(1, 100),
            'is_published' => fake()->boolean(),
        ];
    }

    /**
     * Indicate that the module is published.
     */
    public function published(): ModuleFactory
    {
        return $this->state(fn (array $attributes): array => [
            'is_published' => true,
        ]);
    }

    /**
     * Indicate that the module is unpublished.
     */
    public function unpublished(): ModuleFactory
    {
        return $this->state(fn (array $attributes): array => [
            'is_published' => false,
        ]);
    }

    /**
     * Set a specific order for the module.
     */
    public function withOrder(int $order): ModuleFactory
    {
        return $this->state(fn (array $attributes): array => [
            'order' => $order,
        ]);
    }
}
