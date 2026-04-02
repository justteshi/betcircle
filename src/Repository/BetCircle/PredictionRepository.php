<?php

declare(strict_types=1);

namespace App\Repository\BetCircle;

use App\Entity\BetCircle\Fixture;
use App\Entity\BetCircle\GameWeek;
use App\Entity\BetCircle\Prediction;
use App\Entity\BetCircle\Season;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;

final class PredictionRepository extends EntityRepository
{
    /**
     * @return Prediction[]
     */
    public function findByFixture(Fixture $fixture): array
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.fixture = :fixture')
            ->setParameter('fixture', $fixture)
            ->orderBy('p.id', 'ASC')
            ->getQuery()
            ->getResult()
            ;
    }

    /**
     * @return array<int, array{customerId: int, points: string}>
     */
    public function getWeeklyLeaderboardRows(GameWeek $gameWeek): array
    {
        return $this->createQueryBuilder('p')
            ->select('IDENTITY(p.customer) AS customerId')
            ->addSelect('SUM(p.awardedPoints) AS points')
            ->andWhere('p.gameWeek = :gameWeek')
            ->andWhere('p.isScored = :isScored')
            ->setParameter('gameWeek', $gameWeek)
            ->setParameter('isScored', true)
            ->groupBy('p.customer')
            ->orderBy('points', 'DESC')
            ->addOrderBy('customerId', 'ASC')
            ->getQuery()
            ->getArrayResult()
            ;
    }

    /**
     * @return array<int, array{customerId: int, points: string}>
     */
    public function getSeasonLeaderboardRows(Season $season): array
    {
        return $this->createQueryBuilder('p')
            ->select('IDENTITY(p.customer) AS customerId')
            ->addSelect('SUM(p.awardedPoints) AS points')
            ->innerJoin('p.gameWeek', 'gw')
            ->andWhere('gw.season = :season')
            ->andWhere('p.isScored = :isScored')
            ->setParameter('season', $season)
            ->setParameter('isScored', true)
            ->groupBy('p.customer')
            ->orderBy('points', 'DESC')
            ->addOrderBy('customerId', 'ASC')
            ->getQuery()
            ->getArrayResult()
            ;
    }
}
