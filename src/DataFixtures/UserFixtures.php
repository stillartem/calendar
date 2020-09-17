<?php

namespace Calendar\DataFixtures;

use Calendar\Domain\Admin\Infrastructure\DoctrineORM\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixtures extends Fixture
{
    private UserPasswordEncoderInterface $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $usersList = $this->getUsers();
        foreach ($usersList as $item) {
            $user = (new User())
                ->setPhone($item['phone'])
                ->setName($item['name'])
                ->setEmail($item['email']);
            $user->setPassword($this->passwordEncoder->encodePassword($user, $item['password']));
            foreach ($item['roles'] as $role) {
                $user->addRole($role);
            }
            $manager->persist($user);
        }
        $manager->flush();
    }

    /**
     * @return array
     */
    private function getUsers(): array
    {
        return [
            [
                'email' => 'admin@test.com',
                'name' => 'admin',
                'phone' => '000-000-000',
                'password' => 'test',
                'roles' => ['ROLE_SUPER_ADMIN'],
            ],
        ];
    }
}
