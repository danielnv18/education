<?php

declare(strict_types=1);

use App\Data\RoleData;
use App\Enums\RoleEnum;
use Spatie\Permission\Models\Role;

it('creates role data from a model', function (): void {
    $role = Role::query()->where('name', RoleEnum::Teacher->value)->firstOrFail();

    $data = RoleData::from($role);

    expect($data->name)->toBe(RoleEnum::Teacher->value);
});

it('creates role data collection', function (): void {
    $data = RoleData::collect(Role::all());

    expect($data)->toHaveCount(Role::query()->count());
});
