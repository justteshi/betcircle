<?php

declare(strict_types=1);

namespace App\Service\BetCircle\Scoring;

use App\Enum\BetCircle\PredictionOutcome;

final class PredictionOutcomeResolver
{
    public function resolve(int $homeScore, int $awayScore): PredictionOutcome
    {
        if ($homeScore > $awayScore) {
            return PredictionOutcome::HOME_WIN;
        }

        if ($homeScore < $awayScore) {
            return PredictionOutcome::AWAY_WIN;
        }

        return PredictionOutcome::DRAW;
    }
}
