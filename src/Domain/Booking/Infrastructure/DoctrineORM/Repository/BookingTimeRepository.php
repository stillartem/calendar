<?php

namespace Calendar\Domain\Booking\Infrastructure\DoctrineORM\Repository;

use Calendar\Domain\Admin\Infrastructure\DoctrineORM\Entity\Address;
use Calendar\Domain\Admin\Infrastructure\DoctrineORM\Entity\Service;
use Calendar\Domain\Booking\Application\Collection\BookingTimeCollection;
use Calendar\Domain\Booking\Exception\BookingException;
use Calendar\Domain\Booking\Infrastructure\DoctrineORM\Entity\BookingTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class BookingTimeRepository extends ServiceEntityRepository
{
    /**
     * @param ManagerRegistry $registry
     */
    public function __construct(
        ManagerRegistry $registry
    ) {
        parent::__construct($registry, BookingTime::class);
    }

    /**
     * @param Service $service
     * @param Address $address
     *
     * @return BookingTimeCollection
     * @throws BookingException
     */
    public function getAllTimeSlotsByService(Service $service, Address $address): BookingTimeCollection
    {
        $query = $this->_em->getRepository(BookingTime::class)->createQueryBuilder('bt');
        $query
            ->select()
            ->leftJoin('bt.service', 's')
            ->leftJoin('bt.price', 'pr')
            ->leftJoin('s.address', 'a')
            ->where('bt.service = :service_id')
            ->andWhere('s.address = :address_id')
            ->setParameter('service_id', $service->getId())
            ->setParameter('address_id', $address->getId())
            ->orderBy('bt.timeStart');

        $result = $query->getQuery()->getResult();
        if ($result === null) {
            throw new BookingException(BookingException::BOOKING_TIME_NOT_EXIST);
        }

        return new BookingTimeCollection($result);
    }
}
