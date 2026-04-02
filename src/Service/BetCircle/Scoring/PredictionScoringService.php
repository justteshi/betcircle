<?php

declare(strict_types=1);

namespace App\Service\BetCircle\Scoring;

use App\Entity\BetCircle\Fixture;
use App\Entity\BetCircle\Prediction;
use Doctrine\ORM\EntityManagerInterface;

final readonly class PredictionScoringService
{
    public function __construct(
        private PredictionOutcomeResolver $predictionOutcomeResolver,
        private PredictionPointsCalculator $predictionPointsCalculator,
        private EntityManagerInterface $entityManager,
    ) {
    }

    public function scoreFixturePredictions(Fixture $fixture): void
    {
        if (!$fixture->hasResult()) {
            throw new \LogicException('Cannot score predictions for a fixture without a final result.');
        }

        $actualHomeScore = $fixture->getHomeScore();
        $actualAwayScore = $fixture->getAwayScore();

        if (null === $actualHomeScore || null === $actualAwayScore) {
            throw new \LogicException('Fixture result is incomplete.');
        }

        /** @var Prediction[] $predictions */
        $predictions = $this->entityManager
            ->getRepository(Prediction::class)
            ->findBy([
                'fixture' => $fixture,
            ]);

        foreach ($predictions as $prediction) {
            $predictedOutcome = $this->predictionOutcomeResolver->resolve(
                $prediction->getPredictedHomeScore(),
                $prediction->getPredictedAwayScore(),
            );

            $awardedPoints = $this->predictionPointsCalculator->calculate(
                $prediction->getPredictedHomeScore(),
                $prediction->getPredictedAwayScore(),
                $actualHomeScore,
                $actualAwayScore,
            );

            $prediction->setPredictedOutcome($predictedOutcome);
            $prediction->setAwardedPoints($awardedPoints);
            $prediction->setIsScored(true);
        }

        $this->entityManager->flush();
    }
}
