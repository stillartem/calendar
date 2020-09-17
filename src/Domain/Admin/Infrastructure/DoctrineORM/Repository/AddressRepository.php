<?php

namespace Calendar\Domain\Admin\Infrastructure\DoctrineORM\Repository;

use Calendar\Domain\Admin\Infrastructure\DoctrineORM\Entity\Address;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class AddressRepository extends ServiceEntityRepository
{
    /**
     * @param ManagerRegistry $registry
     */
    public function __construct(
        ManagerRegistry $registry
    ) {
        parent::__construct($registry, Address::class);
    }
}
