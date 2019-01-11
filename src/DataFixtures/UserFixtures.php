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

    public function load(ObjectManager $manager): void
    {
        $this->loadUsers($manager);

        $manager->flush();
    }

    private function loadUsers(ObjectManager $manager): void
    {
        foreach ($this->getUserData() as $userData) {
            $user = new User();
            $user->setDisplayName($userData['displayname']);
            $user->setUsername($userData['username']);
            $user->setPassword($this->passwordEncoder->encodePassword($user, $userData['password']));
            $user->setEmail($userData['email']);
            $user->setRoles($userData['roles']);
            $user->setLocale('en');
            $user->setBackendTheme('default');

            $manager->persist($user);
            $this->addReference($userData['username'], $user);
        }

        $manager->flush();
    }

    private function getUserData(): array
    {
        return [
            [
                'displayname' => 'Admin',
                'username' => 'admin',
                'password' => 'admin%1',
                'email' => 'admin@example.org',
                'roles' => ['ROLE_ADMIN'],
            ],
            [
                'displayname' => 'Gekke Henkie',
                'username' => 'henkie',
                'password' => 'henkie%1',
                'email' => 'henkie@example.org',
                'roles' => ['ROLE_EDITOR'],
            ],
            [
                'displayname' => 'Jane Doe',
                'username' => 'jane_admin',
                'password' => 'kitten',
                'email' => 'jane_admin@example.org',
                'roles' => ['ROLE_ADMIN'],
            ],
            [
                'displayname' => 'Tom Doe',
                'username' => 'tom_admin',
                'password' => 'kitten',
                'email' => 'tom_admin@example.org',
                'roles' => ['ROLE_ADMIN'],
            ],
            [
                'displayname' => 'John Doe',
                'username' => 'john_user',
                'password' => 'kitten',
                'email' => 'john_user@example.org',
                'roles' => ['ROLE_USER'],
            ],
        ];
    }
}
