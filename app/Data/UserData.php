<?php

declare(strict_types=1);

namespace App\Data;

use Carbon\CarbonImmutable;
use Illuminate\Support\Collection;
use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Attributes\Validation\Date;
use Spatie\LaravelData\Attributes\Validation\Email;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapInputName(SnakeCaseMapper::class)]
final class UserData extends Data
{
    public function __construct(
        public ?int $id,
        public string $name,
        #[Email]
        public string $email,
        public ?string $avatar,
        #[Date]
        public CarbonImmutable $createdAt,
        #[Date]
        public ?CarbonImmutable $emailVerifiedAt,
        /** @var Collection<int, RoleData> */
        #[DataCollectionOf(RoleData::class)]
        public Collection $roles = new Collection([]),
    ) {}
}
