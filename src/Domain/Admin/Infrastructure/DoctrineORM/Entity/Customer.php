<?php

namespace Calendar\Domain\Admin\Infrastructure\DoctrineORM\Entity;

use Calendar\Domain\Booking\Infrastructure\DoctrineORM\Entity\Booking;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="cl_customer")
 */
class Customer
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
     * @ORM\Column(type="string",nullable=true)
     */
    protected ?string $firstName;

    /**
     * @var string
     *
     * @ORM\Column(type="string",nullable=true)
     */
    protected ?string $lastName;

    /**
     * @var string
     *
     * @ORM\Column(type="string",nullable=true)
     */
    protected ?string $email = null;

    /**
     * @var string
     *
     * @ORM\Column(type="string",nullable=true)
     */
    protected ?string $phone = null;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean", options={"default"=true})
     */
    protected bool $isAnonymous = true;

    /**
     * @var \DateTimeInterface
     *
     * @ORM\Column(type="datetime")
     */
    protected $createdDate;

    /**
     * @var Booking[]
     *
     * @ORM\OneToMany(targetEntity="Calendar\Domain\Booking\Infrastructure\DoctrineORM\Entity\Booking", mappedBy="customer", cascade={"persist"})
     */
    protected $bookings;


    public function __construct()
    {
        if ($this->createdDate === null) {
            $this->createdDate = new \DateTime('now');
        }
        if ($this->bookings === null) {
            $this->bookings = new ArrayCollection();
        }
    }


    /**
     * @return \DateTime
     */
    public function getCreatedDate()
    {
        return $this->createdDate;
    }


    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param string $name
     *
     * @return Customer
     */
    public function setFirstName(string $name): self
    {
        $this->firstName = $name;

        return $this;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    /**
     * @param string $lastName
     *
     * @return Customer
     */
    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }


    /**
     * @param string $email
     *
     * @return User
     */
    public function setEmail($email): self
    {
        $this->email = $email;

        return $this;
    }


    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }


    /**
     * @param string $phone
     *
     * @return Customer
     */
    public function setPhone($phone): self
    {
        $this->phone = $phone;

        return $this;
    }


    /**
     * @return string
     */
    public function getPhone(): string
    {
        return $this->phone;
    }


    /**
     * @return ArrayCollection
     */
    public function getBookings()
    {
        return $this->bookings;
    }


    /**
     * @param Booking $booking
     *
     * @return Customer
     */
    public function setBookings(Booking $booking): self
    {
        $this->bookings->set($booking->getId(), $booking);

        return $this;
    }

    public static function getAnonymousCustomer()
    {

    }

    public function __toString()
    {
        return (string)$this->firstName;
    }
}
