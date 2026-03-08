<?php

declare(strict_types=1);

namespace App\Enum\BetCircle;

enum WeekEntryStatus: string
{
    case CONFIRMED = 'confirmed';
    case CANCELLED = 'cancelled';
    case REFUNDED = 'refunded';

    public function isConfirmed(): bool
    {
        return $this === self::CONFIRMED;
    }
}
