<?php

namespace Calendar\Domain\Admin\Infrastructure\DoctrineORM\Entity;

use Calendar\Domain\Booking\Infrastructure\DoctrineORM\Entity\BookingTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="cl_service")
 */
class Service
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
     * @var Address
     *
     * @ORM\ManyToOne(targetEntity="Calendar\Domain\Admin\Infrastructure\DoctrineORM\Entity\Address", inversedBy="services", cascade={"persist"})
     */
    protected ?Address $address = null;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     */
    protected ?string $type = null;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     */
    protected ?string $label = null;

    /**
     * @var integer
     *
     * @ORM\Column(type="integer")
     */
    protected ?int $maxSlots = null;

    /**
     * @var Collection
     *
     * @ORM\OneToMany(targetEntity="Calendar\Domain\Booking\Infrastructure\DoctrineORM\Entity\BookingTime", mappedBy="service", cascade={"persist"})
     */
    protected $bookingTimes;

    public function __construct()
    {
        $this->bookingTimes = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param string $type
     *
     * @return Service
     */
    public function setType($type): Service
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return string
     */
    public function getType(): ?string
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string)$this->label;
    }

    /**
     * @param string $label
     *
     * @return Service
     */
    public function setLabel(string $label): Service
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
     * @return int
     */
    public function getMaxSlots(): ?int
    {
        return $this->maxSlots;
    }

    /**
     * @param int $maxSlots
     */
    public function setMaxSlots(int $maxSlots): void
    {
        $this->maxSlots = $maxSlots;
    }

    /**
     * @return Address
     */
    public function getAddress(): ?Address
    {
        return $this->address;
    }

    /**
     * @param Address $address
     *
     * @return Service
     */
    public function setAddress(?Address $address): Service
    {
        $this->address = $address;

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
     * @param BookingTime $bookingTime
     *
     * @return Service
     */
    public function addBookingTime(BookingTime $bookingTime): self
    {
        if (!$this->bookingTimes->contains($bookingTime)) {
            $this->bookingTimes->add($bookingTime);
        }

        $bookingTime->setService($this);

        return $this;
    }

    /**
     * @param BookingTime $bookingTime
     *
     * @return Service
     */
    public function removeBookingTime(BookingTime $bookingTime): self
    {
        if ($this->bookingTimes->contains($bookingTime)) {
            $this->bookingTimes->removeElement($bookingTime);
        }
        $bookingTime->setService(null);

        return $this;
    }
}
