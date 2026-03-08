<?php

declare(strict_types=1);

namespace App\Enum\BetCircle;

enum GameWeekStatus: string
{
    case DRAFT = 'draft';
    case OPEN = 'open';
    case LOCKED = 'locked';
    case COMPLETED = 'completed';
    case CANCELLED = 'cancelled';

    public function isOpen(): bool
    {
        return $this === self::OPEN;
    }

    public function isLocked(): bool
    {
        return $this === self::LOCKED;
    }

    public function isCompleted(): bool
    {
        return $this === self::COMPLETED;
    }
}
