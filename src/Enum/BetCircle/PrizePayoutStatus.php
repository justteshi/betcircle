<?php

declare(strict_types=1);

namespace App\Enum\BetCircle;

enum PrizePayoutStatus: string
{
    case AVAILABLE = 'available';
    case REQUESTED = 'requested';
    case APPROVED = 'approved';
    case PAID = 'paid';
    case REJECTED = 'rejected';

    public function isAvailable(): bool
    {
        return $this === self::AVAILABLE;
    }
}
