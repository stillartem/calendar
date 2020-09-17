<?php

namespace Calendar\Domain\Booking\Infrastructure\DoctrineORM\Entity;

use Calendar\Domain\Admin\Infrastructure\DoctrineORM\Entity\Price;
use Calendar\Domain\Admin\Infrastructure\DoctrineORM\Entity\Service;
use DateTimeZone;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="cl_booking_time")
 */
class BookingTime
{
    public const APPLICATION_DATETIME_FORMAT = 'Y-m-d';

    public const APPLICATION_TIME_FORMAT = 'H:i';

    public const APPLICATION_TIME_ZONE = 'Europe/Kiev';

    /**
     * @var int
     *
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    protected int $id;

    /**
     * @var \DateTimeInterface
     *
     * @ORM\Column(type="time")
     */
    protected ?\DateTimeInterface $timeStart = null;

    /**
     * @var \DateTimeInterface
     *
     * @ORM\Column(type="time")
     */
    protected ?\DateTimeInterface $timeEnd = null;

    /**
     * @var Service
     *
     * @ORM\ManyToOne(targetEntity="Calendar\Domain\Admin\Infrastructure\DoctrineORM\Entity\Service", inversedBy="bookingTimes")
     * @ORM\JoinColumn(name="service_id", referencedColumnName="id")
     */
    protected ?Service $service = null;

    /**
     * @var Price
     *
     * @ORM\ManyToOne(targetEntity="Calendar\Domain\Admin\Infrastructure\DoctrineORM\Entity\Price", inversedBy="bookingTimes", cascade={"persist"})
     */
    protected ?Price $price = null;


    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }


    /**
     * @param \DateTimeInterface $timeStart
     *
     * @return BookingTime
     */
    public function setTimeStart(\DateTimeInterface $timeStart): BookingTime
    {
        $this->timeStart = $timeStart;

        return $this;
    }


    /**
     * @return \DateTimeInterface
     */
    public function getTimeStart(): ?\DateTimeInterface
    {
        return $this->timeStart;
    }


    /**
     * @param \DateTimeInterface $timeEnd
     *
     * @return BookingTime
     */
    public function setTimeEnd(\DateTimeInterface $timeEnd): BookingTime
    {
        $this->timeEnd = $timeEnd;

        return $this;
    }


    /**
     * @return \DateTimeInterface
     */
    public function getTimeEnd(): ?\DateTimeInterface
    {
        return $this->timeEnd;
    }

    /**
     * @param Service $service
     *
     * @return BookingTime
     */
    public function setService(?Service $service): BookingTime
    {
        $this->service = $service;

        return $this;
    }

    /**
     * @return Service
     */
    public function getService(): ?Service
    {
        return $this->service;
    }


    /**
     * @return string
     */
    public function getDuration(): string
    {
        return $this->getTimeStart()->format(self::APPLICATION_TIME_FORMAT)
            . ' - '
            . $this->getTimeEnd()->format(self::APPLICATION_TIME_FORMAT);
    }

    /**
     * @param Price $price
     *
     * @return BookingTime
     */
    public function setPrice(Price $price): BookingTime
    {
        $this->price = $price;

        return $this;
    }

    /**
     * @return Price
     */
    public function getPrice(): ?Price
    {
        return $this->price;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        $service = $this->getService();
        if ($service === null) {
            return $this->getDuration();
        }

        return $this->getService()->getLabel() . ' (' . $this->getDuration() . ')';
    }

    /**
     * @return int
     */
    public function getMaxSlots(): ?int
    {
        return $this->service->getMaxSlots();
    }

    /**
     * @param \DateTime $date
     *
     * @return bool
     * @throws \Exception
     */
    public function isAvailable(\DateTime $date): bool
    {
        $currentTime = new \DateTime('now', new DateTimeZone(self::APPLICATION_TIME_ZONE));
        $timeSlotStart = \DateTime::createFromFormat(
            self::APPLICATION_TIME_FORMAT, $this->getTimeStart()->format(self::APPLICATION_TIME_FORMAT),
            new DateTimeZone(self::APPLICATION_TIME_ZONE)
        );
        if ($currentTime < $date) {
            return true;
        }
        if ($timeSlotStart > $currentTime) {
            return true;
        }

        return false;
    }
}
