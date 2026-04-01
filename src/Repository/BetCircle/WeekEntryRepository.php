<?php

declare(strict_types=1);

namespace App\Repository\BetCircle;

use App\Entity\BetCircle\GameWeek;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Sylius\Component\Customer\Model\CustomerInterface;

final class WeekEntryRepository extends EntityRepository
{
    public function existsForCustomerAndGameWeek(CustomerInterface $customer, GameWeek $gameWeek): bool
    {
        $count = $this->createQueryBuilder('we')
            ->select('COUNT(we.id)')
            ->andWhere('we.customer = :customer')
            ->andWhere('we.gameWeek = :gameWeek')
            ->setParameter('customer', $customer)
            ->setParameter('gameWeek', $gameWeek)
            ->getQuery()
            ->getSingleScalarResult()
        ;

        return (int) $count > 0;
    }
}
