<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\RoleEnum;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
final class UserFactory extends Factory
{
    private static ?string $password = null;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => self::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
            'two_factor_secret' => Str::random(10),
            'two_factor_recovery_codes' => Str::random(10),
            'two_factor_confirmed_at' => now(),
        ];
    }

    public function unverified(): self
    {
        return $this->state(fn (array $attributes): array => [
            'email_verified_at' => null,
        ]);
    }

    public function withoutTwoFactor(): self
    {
        return $this->state(fn (array $attributes): array => [
            'two_factor_secret' => null,
            'two_factor_recovery_codes' => null,
            'two_factor_confirmed_at' => null,
        ]);
    }

    public function asAdmin(): self
    {
        return $this->afterCreating(fn (User $user) => $user->syncRoles([RoleEnum::Admin->value]));
    }

    public function asContentManager(): self
    {
        return $this->afterCreating(fn (User $user) => $user->syncRoles([RoleEnum::ContentManager->value]));
    }

    public function asTeacher(): self
    {
        return $this->afterCreating(fn (User $user) => $user->syncRoles([RoleEnum::Teacher->value]));
    }

    /**
     * @param  array<RoleEnum>  $roles
     */
    public function withRoles(array $roles): self
    {
        return $this->afterCreating(
            fn (User $user) => $user->syncRoles(array_map(fn (RoleEnum $role) => $role->value, $roles))
        );
    }
}
