<?php

namespace Calendar\Domain\Booking\Application;

use Calendar\Domain\Admin\Infrastructure\DoctrineORM\Entity\Address;
use Calendar\Domain\Admin\Infrastructure\DoctrineORM\Entity\Customer;
use Calendar\Domain\Admin\Infrastructure\DoctrineORM\Entity\Service;
use Calendar\Domain\Admin\Infrastructure\DoctrineORM\Entity\User;
use Calendar\Domain\Admin\Infrastructure\DoctrineORM\Repository\CustomerRepository;
use Calendar\Domain\Admin\Infrastructure\DoctrineORM\Repository\ServiceRepository;
use Calendar\Domain\Admin\Infrastructure\DoctrineORM\Repository\UsersRepository;
use Calendar\Domain\Booking\Application\Collection\BookingCollection;
use Calendar\Domain\Booking\Application\Collection\BookingTimeCollection;
use Calendar\Domain\Booking\Application\ValueObject\CustomerBookData;
use Calendar\Domain\Booking\Exception\BookingException;
use Calendar\Domain\Booking\Infrastructure\DoctrineORM\Entity\Booking;
use Calendar\Domain\Booking\Infrastructure\DoctrineORM\Entity\BookingTime;
use Calendar\Domain\Booking\Infrastructure\DoctrineORM\Repository\BookingRepository;
use Calendar\Domain\Booking\Infrastructure\DoctrineORM\Repository\BookingTimeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Twig\Error\Error;

class BookingProcessor
{

    /** @var BookingRepository */
    private BookingRepository $bookingRepository;

    /** @var BookingTimeRepository */
    private BookingTimeRepository $bookingTimeRepository;

    /** @var ServiceRepository */
    private ServiceRepository $serviceRepository;

    /** @var CustomerRepository */
    private CustomerRepository $customerRepository;

    /** @var EntityManagerInterface */
    private EntityManagerInterface $entityManager;


    /**
     *
     * @param BookingRepository $bookingRepository
     * @param BookingTimeRepository $bookingTimeRepository
     * @param ServiceRepository $serviceRepository
     * @param CustomerRepository $customerRepository
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(
        BookingRepository $bookingRepository,
        BookingTimeRepository $bookingTimeRepository,
        ServiceRepository $serviceRepository,
        CustomerRepository $customerRepository,
        EntityManagerInterface $entityManager
    ) {
        $this->bookingRepository = $bookingRepository;
        $this->bookingTimeRepository = $bookingTimeRepository;
        $this->serviceRepository = $serviceRepository;
        $this->customerRepository = $customerRepository;
        $this->entityManager = $entityManager;
    }


    /**
     * @param CustomerBookData $customerBookData
     *
     * @return bool
     * @throws BookingException
     * @throws BookingException
     * @throws Error
     */
    public function processBooking(CustomerBookData $customerBookData): bool
    {
        $this->validateBooking($customerBookData);

        $this->entityManager->beginTransaction();
        try {
            $customer = $this->createCustomer($customerBookData);
            $booking = $this->createBooking($customerBookData, $customer);
        } catch (\Exception $exception) {
            $this->entityManager->rollBack();
            throw new BookingException(BookingException::BOOKING_NOT_EXIST);
        }

        $this->entityManager->commit();

        return true;

    }


    /**
     * @return array
     * @throws BookingException
     */
    public function getAvailableServices(): array
    {
        $services = $this->serviceRepository->getAllActiveServices();
        if (empty($services)) {
            throw new BookingException(BookingException::SERVICES_NOT_EXIST);
        }
        $availableServices = [];
        foreach ($services as $service) {
            $availableServices[] = [
                'service-id' => $service->getId(),
                'label' => $service->getLabel(),
            ];
        }

        return $availableServices;
    }


    /**
     * @param \DateTime $date
     * @param Service $service
     * @param Address $address
     *
     * @return array
     * @throws \Exception
     */
    public function getTimeSlotsForDay(\DateTime $date, Service $service, Address $address): array
    {
        $allTimeSlots = $this->bookingTimeRepository->getAllTimeSlotsByService($service, $address);
        $bookedTimeSlots = $this->bookingRepository->getBookedTimeSlotsForDate($date);

        return $this->mapTimeSlotsForDayAsArray($allTimeSlots, $bookedTimeSlots, $date);
    }


    /**
     * @param BookingTimeCollection $bookingTimeCollection
     * @param BookingCollection $bookingCollection
     * @param \DateTime $date
     *
     * @return array
     * @throws \Exception
     */
    private function mapTimeSlotsForDayAsArray(
        BookingTimeCollection $bookingTimeCollection,
        BookingCollection $bookingCollection, \DateTime $date
    ): array {
        $timeSlotForDay = [];
        /** @var BookingTime $bookingTime */
        foreach ($bookingTimeCollection as $bookingTime) {
            $freeSlots = $bookingTime->getMaxSlots() - $bookingCollection->findCountBooksForSlot($bookingTime->getId());
            $timeSlotForDay[] = [
                'booking-time-id' => $bookingTime->getId(),
                'isAvailable' => $bookingTime->isAvailable($date) && $freeSlots > 0,
                'duration' => $bookingTime->getDuration(),
                'service-id' => $bookingTime->getService()->getId(),
                'address-id' => $bookingTime->getService()->getAddress()->getId(),
                'free-slots' => $freeSlots,
                'price' => $bookingTime->getPrice() ? $bookingTime->getPrice()->getPriceByDate($date) : null,
            ];
        }

        return $timeSlotForDay;
    }


    /**
     * @param CustomerBookData $customerBookData
     *
     * @throws BookingException
     */
    private function checkForFreeTimeSlot(CustomerBookData $customerBookData): void
    {
        $busySlots =
            $this->bookingRepository
                ->getBookedTimeSlotsForDate($customerBookData->getBookingDate())
                ->findCountBooksForSlot($customerBookData->getBookingTime()->getId());

        $maxSlots = $customerBookData->getBookingTime()->getMaxSlots();
        $toBeBookSlots = $customerBookData->getPersons()->getValue();

        if ($busySlots + $toBeBookSlots > $maxSlots) {
            throw new BookingException(BookingException::WRONG_TIME_SLOT);
        }
    }


    /**
     * @param CustomerBookData $customerBookData
     *
     * @throws BookingException
     */
    private function validateBooking(CustomerBookData $customerBookData): void
    {
        $this->checkForFreeTimeSlot($customerBookData);
    }

    /**
     * @param CustomerBookData $customerBookData
     * @param Customer $user
     *
     * @return Booking
     * @throws ORMException
     * @throws OptimisticLockException
     */
    private function createBooking(CustomerBookData $customerBookData, Customer $user): Booking
    {
        $booking = (new Booking())
            ->setCustomer($user)
            ->setBookingDate($customerBookData->getBookingDate())
            ->setBookingTime($customerBookData->getBookingTime())
            ->setApproved(false)
            ->setBookedSlots($customerBookData->getPersons()->getValue());

        $this->bookingRepository->save($booking);

        return $booking;
    }


    /**
     * @param CustomerBookData $customerBookData
     *
     * @return Customer
     * @throws ORMException
     * @throws OptimisticLockException
     */
    private function createCustomer(CustomerBookData $customerBookData): Customer
    {
        $customer = $this->customerRepository->findOneBy(
            [
                'phone' => (string)$customerBookData->getPhone(),
                'email' => (string)$customerBookData->getEmail(),

            ]
        );
        if ($customer === null) {
            $customer = (new Customer())
                ->setFirstName($customerBookData->getName())
                ->setPhone($customerBookData->getPhone());
            $this->customerRepository->save($customer);
        }

        return $customer;
    }
}
