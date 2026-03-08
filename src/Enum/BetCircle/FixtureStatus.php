<?php

declare(strict_types=1);

namespace App\Enum\BetCircle;

enum FixtureStatus: string
{
    case SCHEDULED = 'scheduled';
    case FINISHED = 'finished';
    case POSTPONED = 'postponed';
    case CANCELLED = 'cancelled';

    public function isFinished(): bool
    {
        return $this === self::FINISHED;
    }

    public function isPlayable(): bool
    {
        return $this === self::SCHEDULED;
    }
}
