<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\File>
 */
final class FileFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $extension = fake()->randomElement(['jpg', 'png', 'pdf', 'doc', 'txt']);
        $mimeTypes = [
            'jpg' => 'image/jpeg',
            'png' => 'image/png',
            'pdf' => 'application/pdf',
            'doc' => 'application/msword',
            'txt' => 'text/plain',
        ];

        return [
            'name' => fake()->word().'.'.$extension,
            'path' => 'files/'.fake()->uuid().'.'.$extension,
            'mime_type' => $mimeTypes[$extension],
            'extension' => $extension,
            'size' => fake()->numberBetween(1024, 10485760), // 1KB to 10MB
            'disk' => 'local',
            'uploaded_by' => User::factory(),
            'uploaded_at' => now(),
            'fileable_id' => null,
            'fileable_type' => null,
        ];
    }
}
