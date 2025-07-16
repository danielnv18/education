<?php

declare(strict_types=1);

namespace App\Data;

use Carbon\CarbonImmutable;
use Illuminate\Support\Collection;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Attributes\Validation\Date;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapInputName(SnakeCaseMapper::class)]
final class UserData extends Data
{
    public function __construct(
        public ?int $id,
        public string $name,
        public string $email,
        #[Date]
        public CarbonImmutable $createdAt,
        #[Date]
        public ?CarbonImmutable $emailVerifiedAt,
        /** @var Collection<int, RoleData> */
        public Collection $roles = new Collection([]),
    ) {}
}
