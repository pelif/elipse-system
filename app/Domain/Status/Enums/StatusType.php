<?php

namespace App\Domain\Status\Enums;

enum StatusType: int
{
    case JOB = 1;
    case ORDER = 2;

    public static function valueFromLabel(string $label): int
    {
        return match ($label) {
            'job' => self::JOB->value,
            'os' => self::ORDER->value,
            default => throw new \InvalidArgumentException('Invalid status type value'),
        };
    }
}

