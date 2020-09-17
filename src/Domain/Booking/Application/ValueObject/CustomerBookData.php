<?php

namespace Calendar\Domain\Booking\Application\ValueObject;

use Calendar\SharedKernel\ValueObject;
use Calendar\Domain\Booking\Infrastructure\DoctrineORM\Entity\BookingTime;

final class CustomerBookData implements ValueObject
{
    /** @var Email */
    private ?Email $email;

    /** @var Phone */
    private Phone $phone;

    /** @var Name */
    private Name $name;

    /** @var Persons */
    private Persons $persons;

    /** @var BookingTime */
    private BookingTime $bookingTime;

    /** @var \DateTime */
    private \DateTime $bookingDate;


    /**
     *
     * @param Name $name
     * @param Phone $phone
     * @param Persons $persons
     * @param BookingTime $bookingTime
     * @param \DateTime $date
     * @param Email|null $email
     */
    public function __construct(Name $name, Phone $phone, Persons $persons, BookingTime $bookingTime, \DateTime $date, Email $email = null)
    {
        $this->phone = $phone;
        $this->name = $name;
        $this->persons = $persons;
        $this->bookingTime = $bookingTime;
        $this->bookingDate = $date;
        $this->email = $email;
    }


    public function getAsArray(): array
    {
        return
            [
                'name' => $this->getName(),
                'phone' => $this->getPhone(),
                'persons' => $this->getPersons(),
                'booking-time-id' => $this->bookingTime->getId(),
                'date' => $this->bookingDate->format(BookingTime::APPLICATION_DATETIME_FORMAT),
            ];
    }


    /**
     * @return Persons
     */
    public function getPersons(): Persons
    {
        return $this->persons;
    }


    /**
     * @return Name
     */
    public function etName(): Name
    {
        return $this->name;
    }


    /**
     * @return Phone
     */
    public function getPhone(): Phone
    {
        return $this->phone;
    }


    /**
     * @return Email
     */
    public function getEmail(): ?Email
    {
        return $this->email;
    }


    /**
     * @return BookingTime
     */
    public function getBookingTime(): BookingTime
    {
        return $this->bookingTime;
    }


    /**
     * @return \DateTime
     */
    public function getBookingDate(): \DateTime
    {
        return $this->bookingDate;
    }


    /**
     * @return Name
     */
    public function getName(): Name
    {
        return $this->name;
    }
}
