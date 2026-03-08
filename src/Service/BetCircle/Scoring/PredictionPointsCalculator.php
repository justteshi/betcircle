<?php

declare(strict_types=1);

namespace App\Service\BetCircle\Scoring;

final readonly class PredictionPointsCalculator
{
    public function __construct(
        private PredictionOutcomeResolver $predictionOutcomeResolver,
    ) {
    }

    public function calculate(
        int $predictedHomeScore,
        int $predictedAwayScore,
        int $actualHomeScore,
        int $actualAwayScore,
    ): int {
        if (
            $predictedHomeScore === $actualHomeScore &&
            $predictedAwayScore === $actualAwayScore
        ) {
            return 3;
        }

        $predictedOutcome = $this->predictionOutcomeResolver->resolve(
            $predictedHomeScore,
            $predictedAwayScore,
        );

        $actualOutcome = $this->predictionOutcomeResolver->resolve(
            $actualHomeScore,
            $actualAwayScore,
        );

        if ($predictedOutcome === $actualOutcome) {
            return 1;
        }

        return 0;
    }
}
