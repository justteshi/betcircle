<?php

declare(strict_types=1);

namespace App\Enum\BetCircle;

enum SeasonStatus: string
{
    case DRAFT = 'draft';
    case ACTIVE = 'active';
    case COMPLETED = 'completed';
    case CANCELLED = 'cancelled';

    public function isActive(): bool
    {
        return $this === self::ACTIVE;
    }

    public function isCompleted(): bool
    {
        return $this === self::COMPLETED;
    }
}
