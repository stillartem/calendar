<?php

namespace Calendar\Domain\Booking\Infrastructure\DoctrineORM\Repository;

use Calendar\Domain\Booking\Application\Collection\BookingCollection;
use Calendar\Domain\Booking\Exception\BookingException;
use Calendar\Domain\Booking\Infrastructure\DoctrineORM\Entity\Booking;
use Calendar\Domain\Booking\Infrastructure\DoctrineORM\Entity\BookingTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

class BookingRepository extends ServiceEntityRepository
{
    /**
     * @param ManagerRegistry $registry
     */
    public function __construct(
        ManagerRegistry $registry
    ) {
        parent::__construct($registry, Booking::class);
    }

    /**
     * @param \DateTime $dateTime
     *
     * @return BookingCollection
     * @throws BookingException
     */
    public function getBookedTimeSlotsForDate(\DateTime $dateTime): BookingCollection
    {
        $query = $this->_em->getRepository(Booking::class)->createQueryBuilder('b');
        $query
            ->select()
            ->where('b.bookingDate = :booking_date')
            ->setParameter('booking_date', $dateTime->format(BookingTime::APPLICATION_DATETIME_FORMAT));

        $result = $query->getQuery()->getResult();
        if ($result === null) {
            throw new BookingException(BookingException::BOOKING_NOT_EXIST);
        }

        return new BookingCollection($result);
    }


    /**
     * @param Booking $booking
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function save(Booking $booking)
    {
        $this->_em->persist($booking);
        $this->_em->flush();
    }
}
