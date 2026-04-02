<?php

declare(strict_types=1);

namespace App\Repository\BetCircle;

use App\Entity\BetCircle\GameWeek;
use App\Enum\BetCircle\StandingSnapshotType;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;

final class StandingSnapshotRepository extends EntityRepository
{
    public function findWeeklyByGameWeek(GameWeek $gameWeek): array
    {
        return $this->createQueryBuilder('ss')
            ->addSelect('customer')
            ->leftJoin('ss.customer', 'customer')
            ->andWhere('ss.gameWeek = :gameWeek')
            ->andWhere('ss.type = :type')
            ->setParameter('gameWeek', $gameWeek)
            ->setParameter('type', StandingSnapshotType::WEEKLY)
            ->orderBy('ss.rank', 'ASC')
            ->addOrderBy('ss.points', 'DESC')
            ->addOrderBy('customer.id', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }
}
