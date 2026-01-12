<?php

declare(strict_types=1);

namespace App\Data;

use Carbon\CarbonImmutable;
use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Attributes\Validation\Date;
use Spatie\LaravelData\Attributes\Validation\Email;
use Spatie\LaravelData\Data;

final class UserData extends Data
{
    public function __construct(
        public int $id,
        public string $name,
        #[Email]
        public string $email,
        #[Date]
        public ?CarbonImmutable $emailVerifiedAt,
        #[Date]
        public CarbonImmutable $createdAt,
        #[Date]
        public CarbonImmutable $updatedAt,
        /** @var \Illuminate\Support\Collection<int, RoleData> */
        #[DataCollectionOf(RoleData::class)]
        public \Illuminate\Support\Collection $roles,
        public ?string $avatar = null,
    ) {}
}
