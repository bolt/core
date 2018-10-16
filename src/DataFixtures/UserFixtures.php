<?php

declare(strict_types=1);

namespace Bolt\DataFixtures;

use Bolt\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixtures extends Fixture
{
    /** @var UserPasswordEncoderInterface */
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function load(ObjectManager $manager)
    {
        $this->loadUsers($manager);

        $manager->flush();
    }

    private function loadUsers(ObjectManager $manager)
    {
        foreach ($this->getUserData() as [$fullname, $username, $password, $email, $roles]) {
            $user = new User();
            $user->setFullName($fullname);
            $user->setUsername($username);
            $user->setPassword($this->passwordEncoder->encodePassword($user, $password));
            $user->setEmail($email);
            $user->setRoles($roles);

            $manager->persist($user);
            $this->addReference($username, $user);
        }

        $manager->flush();
    }

    private function getUserData(): array
    {
        return [
            // $userData = [$fullname, $username, $password, $email, $roles];
            ['Admin', 'admin', 'admin%1', 'admin@example.org', ['ROLE_ADMIN']],
            ['Gekke Henkie', 'henkie', 'henkie%1', 'henkie@example.org', ['ROLE_EDITOR']],
            ['Jane Doe', 'jane_admin', 'kitten', 'jane_admin@example.org', ['ROLE_ADMIN']],
            ['Tom Doe', 'tom_admin', 'kitten', 'tom_admin@example.org', ['ROLE_ADMIN']],
            ['John Doe', 'john_user', 'kitten', 'john_user@example.org', ['ROLE_USER']],
        ];
    }
}
