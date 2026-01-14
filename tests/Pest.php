<?php

declare(strict_types=1);

use Database\Seeders\DatabaseSeeder;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Sleep;
use Illuminate\Support\Str;

pest()->extend(Tests\TestCase::class)
    ->use(Illuminate\Foundation\Testing\RefreshDatabase::class)
    ->beforeEach(function (): void {
        Str::createRandomStringsNormally();
        Str::createUuidsNormally();
        Http::preventStrayRequests();
        Sleep::fake();

        $this->freezeTime();
        $this->seed(DatabaseSeeder::class);
    })
    ->in('Feature', 'Unit');

expect()->extend('toBeOne', fn () => $this->toBe(1));

function something(): void {}
