<?php

declare(strict_types=1);

namespace App\Enum\BetCircle;

enum PrizePayoutType: string
{
    case WEEKLY = 'weekly';
    case SEASONAL = 'seasonal';
}
