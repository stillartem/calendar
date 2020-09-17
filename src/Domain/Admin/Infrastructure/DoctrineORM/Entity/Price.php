<?php

namespace Calendar\Domain\Admin\Infrastructure\DoctrineORM\Entity;

use Calendar\Domain\Booking\Infrastructure\DoctrineORM\Entity\BookingTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="Calendar\Domain\Admin\Infrastructure\DoctrineORM\Repository\PriceRepository")
 * @ORM\Table(name="cl_price")
 */
class Price
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
     * @var float
     *
     * @ORM\Column(type="float")
     */
    protected ?float $mondayAmount = null;

    /**
     * @var float
     *
     * @ORM\Column(type="float")
     */
    protected ?float $tuesdayAmount = null;

    /**
     * @var float
     *
     * @ORM\Column(type="float")
     */
    protected ?float $wednesdayAmount = null;

    /**
     * @var float
     *
     * @ORM\Column(type="float")
     */
    protected ?float $thursdayAmount = null;

    /**
     * @var float
     *
     * @ORM\Column(type="float")
     */
    protected ?float $fridayAmount = null;

    /**
     * @var float
     *
     * @ORM\Column(type="float")
     */
    protected ?float $saturdayAmount = null;

    /**
     * @var float
     *
     * @ORM\Column(type="float")
     */
    protected ?float $sundayAmount = null;

    /**
     * @var BookingTime[]|Collection
     *
     * @ORM\OneToMany(targetEntity="Calendar\Domain\Booking\Infrastructure\DoctrineORM\Entity\BookingTime", mappedBy="price", cascade={"persist"})
     */
    protected $bookingTimes;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     */
    protected ?string $label = null;


    public function __construct()
    {
        if ($this->bookingTimes === null) {
            $this->bookingTimes = new ArrayCollection();
        }
    }


    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }


    /**
     * @param float $mondayAmount
     *
     * @return Price
     */
    public function setMondayAmount(float $mondayAmount): Price
    {
        $this->mondayAmount = $mondayAmount;

        return $this;
    }


    /**
     * @return float
     */
    public function getMondayAmount(): ?float
    {
        return $this->mondayAmount;
    }


    /**
     * @param float $tuesdayAmount
     *
     * @return Price
     */
    public function setTuesdayAmount(float $tuesdayAmount): Price
    {
        $this->tuesdayAmount = $tuesdayAmount;

        return $this;
    }


    /**
     * @return float
     */
    public function getTuesdayAmount(): ?float
    {
        return $this->tuesdayAmount;
    }


    /**
     * @param float $wednesdayAmount
     *
     * @return Price
     */
    public function setWednesdayAmount(float $wednesdayAmount): Price
    {
        $this->wednesdayAmount = $wednesdayAmount;

        return $this;
    }


    /**
     * @return float
     */
    public function getWednesdayAmount(): ?float
    {
        return $this->wednesdayAmount;
    }


    /**
     * @param float $thursdayAmount
     *
     * @return Price
     */
    public function setThursdayAmount(float $thursdayAmount): Price
    {
        $this->thursdayAmount = $thursdayAmount;

        return $this;
    }


    /**
     * @return float
     */
    public function getThursdayAmount(): ?float
    {
        return $this->thursdayAmount;
    }


    /**
     * @param float $fridayAmount
     *
     * @return Price
     */
    public function setFridayAmount(float $fridayAmount): Price
    {
        $this->fridayAmount = $fridayAmount;

        return $this;
    }


    /**
     * @return float
     */
    public function getFridayAmount(): ?float
    {
        return $this->fridayAmount;
    }


    /**
     * @param float $saturdayAmount
     *
     * @return Price
     */
    public function setSaturdayAmount(float $saturdayAmount): Price
    {
        $this->saturdayAmount = $saturdayAmount;

        return $this;
    }


    /**
     * @param float $sundayAmount
     *
     * @return Price
     */
    public function setSundayAmount(float $sundayAmount): Price
    {
        $this->sundayAmount = $sundayAmount;

        return $this;
    }


    /**
     * @return float
     */
    public function getSundayAmount(): ?float
    {
        return $this->sundayAmount;
    }


    /**
     * @return float
     */
    public function getSaturdayAmount(): ?float
    {
        return $this->saturdayAmount;
    }


    /**
     * @param BookingTime $bookingTime
     *
     * @return Price
     */
    public function setBookingTimes(BookingTime $bookingTime): Price
    {
        $this->bookingTimes->set($bookingTime->getId(), $bookingTime);

        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getBookingTimes()
    {
        return $this->bookingTimes;
    }


    /**
     * @param string $label
     *
     * @return Price
     */
    public function setLabel(string $label): Price
    {
        $this->label = $label;

        return $this;
    }


    /**
     * @return string
     */
    public function getLabel(): ?string
    {
        return $this->label;
    }


    /**
     * @param \DateTimeInterface $date
     *
     * @return float
     */
    public function getPriceByDate(\DateTimeInterface $date): ?float
    {
        $day = $date->format('D');
        switch ($day) {
            case 'Mon':
                return $this->getMondayAmount();
            case 'Tue':
                return $this->getTuesdayAmount();
            case 'Wed':
                return $this->getWednesdayAmount();
            case 'Thu':
                return $this->getThursdayAmount();
            case 'Fri':
                return $this->getFridayAmount();
            case 'Sat':
                return $this->getSaturdayAmount();
            case 'Sun':
                return $this->getSundayAmount();
            default:
                return null;
        }
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string)$this->label;
    }

}
