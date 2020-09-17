<?php

namespace Calendar\Domain\Booking\Infrastructure\DoctrineORM\Entity;

use Calendar\Domain\Admin\Infrastructure\DoctrineORM\Entity\Customer;
use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="cl_booking")
 */
class Booking
{

    /**
     * @var int
     *
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    protected int $id;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean", nullable=true, options={"default"=false})
     */
    protected bool $approved = false;

    /**
     * @var integer
     *
     * @ORM\Column(type="integer")
     */
    protected ?int $bookedSlots = null;

    /**
     * @var \DateTimeInterface
     *
     * @ORM\Column(type="datetime")
     */
    protected ?\DateTimeInterface $bookingDate = null;

    /**
     * @var BookingTime
     *
     * @ORM\ManyToOne(targetEntity="Calendar\Domain\Booking\Infrastructure\DoctrineORM\Entity\BookingTime", cascade={"persist"})
     */
    protected ?BookingTime $bookingTime = null;

    /**
     * @var Customer
     *
     * @ORM\ManyToOne(targetEntity="Calendar\Domain\Admin\Infrastructure\DoctrineORM\Entity\Customer", inversedBy="bookings", cascade={"persist"})
     */
    protected ?Customer $customer = null;


    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return \DateTimeInterface
     */
    public function getBookingDate(): ?\DateTimeInterface
    {
        return $this->bookingDate;
    }


    /**
     * @param DateTime $bookingDate
     *
     * @return Booking
     */
    public function setBookingDate(DateTime $bookingDate): Booking
    {
        $this->bookingDate = $bookingDate;

        return $this;
    }


    /**
     * @return BookingTime
     */
    public function getBookingTime(): ?BookingTime
    {
        return $this->bookingTime;
    }


    /**
     * @param BookingTime $bookingTime
     *
     * @return Booking
     */
    public function setBookingTime(BookingTime $bookingTime): Booking
    {
        $this->bookingTime = $bookingTime;

        return $this;
    }


    /**
     * @return int
     */
    public function getBookedSlots(): ?int
    {
        return $this->bookedSlots;
    }


    /**
     * @param int $bookedSlots
     *
     * @return Booking
     */
    public function setBookedSlots(int $bookedSlots): Booking
    {
        $this->bookedSlots = $bookedSlots;

        return $this;
    }


    /**
     * @param bool $approved
     *
     * @return Booking
     */
    public function setApproved(bool $approved): Booking
    {
        $this->approved = $approved;

        return $this;
    }


    /**
     * @return bool
     */
    public function isApproved(): bool
    {
        return $this->approved;
    }


    /**
     * @param Customer $user
     *
     * @return Booking
     */
    public function setCustomer(Customer $user): self
    {
        $this->customer = $user;

        return $this;
    }


    /**
     * @return Customer
     */
    public function getCustomer(): ?Customer
    {
        return $this->customer;
    }

    /**
     * @return float|int
     */
    public function getBookingPrice()
    {
        $price = $this->getBookingTime()->getPrice()->getPriceByDate($this->getBookingDate());

        return $this->getBookedSlots() * $price;

    }


    /**
     * @return DateTime
     */
    public function getBookingDateTimeStart(): DateTime
    {
        $date = $this->getBookingDate()->format(BookingTime::APPLICATION_DATETIME_FORMAT);
        $time = $this->getBookingTime()->getTimeStart()->format(BookingTime::APPLICATION_TIME_FORMAT);

        return DateTime::createFromFormat(
            BookingTime::APPLICATION_DATETIME_FORMAT . ' ' . BookingTime::APPLICATION_TIME_FORMAT,
            $date . ' ' . $time,
            new \DateTimeZone(BookingTime::APPLICATION_TIME_ZONE)
        );
    }


    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getUser()->getName() . ' ' . $this->getBookingDate()->format(
                BookingTime::APPLICATION_DATETIME_FORMAT
            );
    }
}
