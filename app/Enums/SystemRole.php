<?php

declare(strict_types=1);

namespace App\Enums;

enum SystemRole: string
{
    case Admin = 'admin';
    case Teacher = 'teacher';
    case ContentManager = 'content_manager';

    /**
     * @return list<string>
     */
    public static function values(): array
    {
        return array_map(
            static fn (self $role): string => $role->value,
            self::cases(),
        );
    }
}
