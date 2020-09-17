<?php

namespace Calendar\Domain\Admin\Infrastructure\DoctrineORM\Entity;

use Doctrine\Common\Collections\Collection, Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="cl_address")
 */
class Address
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
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    protected ?string $phoneOne = null;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    protected ?string $phoneTwo = null;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    protected ?string $street = null;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    protected ?string $city = null;

    /**
     * @var Collection
     *
     * @ORM\OneToMany(targetEntity="Calendar\Domain\Admin\Infrastructure\DoctrineORM\Entity\Service", mappedBy="address", cascade={"persist"})
     */
    protected Collection $services;


    public function __construct()
    {
        $this->services = new ArrayCollection();
    }


    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }


    /**
     * @param string $phoneOne
     *
     * @return Address
     */
    public function setPhoneOne(string $phoneOne): Address
    {
        $this->phoneOne = $phoneOne;

        return $this;
    }


    /**
     * @return string
     */
    public function getPhoneOne(): ?string
    {
        return $this->phoneOne;
    }


    /**
     * @param string $phoneTwo
     *
     * @return Address
     */
    public function setPhoneTwo(string $phoneTwo): Address
    {
        $this->phoneTwo = $phoneTwo;

        return $this;
    }


    /**
     * @return string
     */
    public function getPhoneTwo(): ?string
    {
        return $this->phoneTwo;
    }


    /**
     * @param string $street
     *
     * @return Address
     */
    public function setStreet($street): Address
    {
        $this->street = $street;

        return $this;
    }


    /**
     * @return string
     */
    public function getStreet(): ?string
    {
        return $this->street;
    }


    /**
     * @param string $city
     *
     * @return Address
     */
    public function setCity($city): Address
    {
        $this->city = $city;

        return $this;
    }


    /**
     * @return string
     */
    public function getCity(): ?string
    {
        return $this->city;
    }


    /**
     * @return string
     */
    public function __toString()
    {
        return (string)$this->street;
    }


    /**
     * @return Collection
     */
    public function getServices()
    {
        return $this->services;
    }

    /**
     * @param Service $service
     *
     * @return $this
     */
    public function addService(Service $service): self
    {
        if (false === $this->services->contains($service)) {
            $this->services->add($service);
        }

        $service->setAddress($this);

        return $this;
    }

    /**
     * @param Service $service
     *
     * @return $this
     */
    public function removeService(Service $service): Address
    {
        if (true === $this->services->contains($service)) {
            $this->services->removeElement($service);
        }

        $service->setAddress(null);

        return $this;
    }

    /**
     * @param Service $service
     *
     * @return self
     */
    public function setBookingTimes(Service $service): self
    {
        $this->services->set($service->getLabel(), $service);

        return $this;
    }
}
