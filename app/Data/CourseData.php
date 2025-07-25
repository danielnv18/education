<?php

declare(strict_types=1);

namespace App\Data;

use App\Enums\CourseStatus;
use Carbon\CarbonImmutable;
use Illuminate\Support\Collection;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Attributes\Validation\Date;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapInputName(SnakeCaseMapper::class)]
final class CourseData extends Data
{
    public function __construct(
        public ?int $id,
        public string $title,
        public ?string $description,
        public CourseStatus $status,
        public ?string $cover,
        #[Date]
        public ?CarbonImmutable $endDate,
        #[Date]
        public ?CarbonImmutable $startDate,
        public ?UserData $teacher,
        public bool $isPublished = false,
        /** @var Collection<int, ModuleData> */
        public Collection $modules = new Collection(),
        /** @var Collection<int, UserData> */
        public Collection $students = new Collection(),
    ) {}
}
