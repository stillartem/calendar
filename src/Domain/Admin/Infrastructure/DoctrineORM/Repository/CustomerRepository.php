<?php

namespace Calendar\Domain\Admin\Infrastructure\DoctrineORM\Repository;

use Calendar\Domain\Admin\Infrastructure\DoctrineORM\Entity\Customer;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

class CustomerRepository extends ServiceEntityRepository
{
    /**
     * @param ManagerRegistry $registry
     */
    public function __construct(
        ManagerRegistry $registry
    ) {
        parent::__construct($registry, Customer::class);
    }

    /**
     * @param Customer $user
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function save(Customer $user): void
    {
        $this->_em->persist($user);
        $this->_em->flush();
    }
}
