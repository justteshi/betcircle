<?php

declare(strict_types=1);

namespace App\Service\BetCircle\Payout;

final class PrizeSplitService
{
    /**
     * @return array<int, int>
     */
    public function splitEqually(int $totalTokens, int $winnerCount): array
    {
        if ($winnerCount <= 0) {
            throw new \InvalidArgumentException('Winner count must be greater than zero.');
        }

        if ($totalTokens < 0) {
            throw new \InvalidArgumentException('Total tokens must be zero or greater.');
        }

        $baseShare = intdiv($totalTokens, $winnerCount);
        $remainder = $totalTokens % $winnerCount;

        $shares = [];

        for ($i = 0; $i < $winnerCount; ++$i) {
            $shares[] = $baseShare + ($i < $remainder ? 1 : 0);
        }

        return $shares;
    }
}
