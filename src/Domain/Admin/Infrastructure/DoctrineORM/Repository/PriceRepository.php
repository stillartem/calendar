<?php

namespace Calendar\Domain\Admin\Infrastructure\DoctrineORM\Repository;

use Calendar\Domain\Admin\Infrastructure\DoctrineORM\Entity\Price;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class PriceRepository extends ServiceEntityRepository
{
    /**
     * @param ManagerRegistry $registry
     */
    public function __construct(
        ManagerRegistry $registry
    ) {
        parent::__construct($registry, Price::class);
    }
}
