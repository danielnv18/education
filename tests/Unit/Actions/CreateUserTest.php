<?php

declare(strict_types=1);

use App\Actions\CreateUser;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Event;

use function Pest\Laravel\seed;

it('may create a user', function (): void {
    seed(RolesAndPermissionsSeeder::class);

    Event::fake([Registered::class]);

    $action = resolve(CreateUser::class);

    $user = $action->handle([
        'name' => 'Test User',
        'email' => 'example@email.com',
    ], 'password');

    expect($user)->toBeInstanceOf(User::class)
        ->and($user->name)->toBe('Test User')
        ->and($user->email)->toBe('example@email.com')
        ->and($user->password)->not->toBe('password')
        ->and($user->roles)->toBeEmpty();
    Event::assertDispatched(Registered::class);
});
