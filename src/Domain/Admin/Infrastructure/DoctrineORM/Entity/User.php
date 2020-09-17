<?php

namespace Calendar\Domain\Admin\Infrastructure\DoctrineORM\Entity;

use Calendar\Domain\Booking\Infrastructure\DoctrineORM\Entity\Booking;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity
 * @ORM\Table(name="cl_user")
 */
class User implements UserInterface
{
    public const ROLE_CUSTOMER = 'CUSTOMER-ROLE';

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
     * @ORM\Column(type="string")
     */
    protected string $username;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     */
    protected string $name;

    /**
     * @var string[]
     * @ORM\Column(type="simple_array")
     */
    protected array $roles = ['ROLE_USER'];

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     */
    protected string $email;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     */
    protected string $phone;

    /**
     * @var \DateTimeInterface
     *
     * @ORM\Column(type="datetime")
     */
    protected \DateTimeInterface $createdDate;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     */
    protected string $password;


    public function __construct()
    {
        $this->createdDate = new \DateTime('now');
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
     * @param string $username
     *
     * @return User
     */
    public function setUsername($username): User
    {
        $this->username = $username;

        return $this;
    }


    /**
     * @return string
     */
    public function getUsername(): string
    {
        return $this->username;
    }


    /**
     * @param string $name
     *
     * @return User
     */
    public function setName($name): User
    {
        $this->name = $name;

        return $this;
    }


    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }


    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @param string $role
     *
     * @return $this
     */
    public function addRole(string $role): User
    {
        if (!in_array($role, $this->roles, true)) {
            $this->roles[] = $role;
        }

        return $this;
    }

    /**
     * @param string $role
     *
     * @return $this
     */
    public function removeRole(string $role): User
    {
        if (false !== $key = array_search($role, $this->roles, true)) {
            unset($this->roles[$key]);
            $this->roles = array_values($this->roles);
        }

        return $this;
    }

    /**
     * @return string|null
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    /**
     * @param string $password
     */
    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    /**
     * @return string|null
     */
    public function getSalt(): ?string
    {
        return null;
    }


    /**
     * @param string $email
     *
     * @return User
     */
    public function setEmail(string $email): User
    {
        $this->email = $email;
        if (empty($this->username)) {
            $this->setUsername(strstr($email, '@', true));
        }

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
     * @return User
     */
    public function setPhone($phone): User
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

    public function __toString()
    {
        return $this->getName();
    }

    public function eraseCredentials(): void
    {
//        $this->email = 'delete_email@gmail.com';
//        $this->phone = '000-000--34';
//        $this->password = 'deleted';
//        $this->username = 'deleted%' . $this->id;
    }
}
