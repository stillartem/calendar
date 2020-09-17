<?php

namespace Calendar\Domain\Admin\Infrastructure\DoctrineORM\Repository;

use Calendar\Domain\Admin\Infrastructure\DoctrineORM\Entity\Service;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class ServiceRepository extends ServiceEntityRepository
{
    /**
     * @param ManagerRegistry $registry
     */
    public function __construct(
        ManagerRegistry $registry
    ) {
        parent::__construct($registry, Service::class);
    }

    /**
     * @return Service[]
     */
    public function getAllActiveServices(): array
    {
        return $this->findAll();
    }
}
