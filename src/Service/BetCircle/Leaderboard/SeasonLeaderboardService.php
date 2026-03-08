<?php

declare(strict_types=1);

namespace App\Service\BetCircle\Leaderboard;

use App\Entity\BetCircle\Season;
use App\Repository\BetCircle\PredictionRepository;

final readonly class SeasonLeaderboardService
{
    public function __construct(
        private PredictionRepository $predictionRepository,
    ) {
    }

    /**
     * @return array<int, array{rank: int, customerId: int, points: int}>
     */
    public function getLeaderboard(Season $season): array
    {
        $rows = $this->predictionRepository->getSeasonLeaderboardRows($season);

        $leaderboard = [];
        $currentRank = 0;
        $previousPoints = null;
        $index = 0;

        foreach ($rows as $row) {
            ++$index;

            $points = (int) $row['points'];

            if ($previousPoints === null || $points < $previousPoints) {
                $currentRank = $index;
            }

            $leaderboard[] = [
                'rank' => $currentRank,
                'customerId' => (int) $row['customerId'],
                'points' => $points,
            ];

            $previousPoints = $points;
        }

        return $leaderboard;
    }
}
