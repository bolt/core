<?php

declare(strict_types=1);

namespace Bolt\DataFixtures;

use Bolt\Common\Str;
use Bolt\Entity\User;
use Bolt\Enum\UserStatus;
use Bolt\Repository\UserRepository;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends BaseFixture implements FixtureGroupInterface
{
    private bool $append = false;

    /** @var array */
    private $allUsers = [];

    public function __construct(
        private readonly UserPasswordHasherInterface $passwordHasher,
        private readonly UserRepository $users
    ) {
        // If ran with `--append` we append users, and use random passwords for them
        if ($this->getOption('--append')) {
            $this->append = true;
        }
    }

    public function load(ObjectManager $manager): void
    {
        $this->getCurrentUsers();
        $this->loadUsers($manager);

        $manager->flush();
    }

    public static function getGroups(): array
    {
        return ['with-images', 'without-images'];
    }

    private function loadUsers(ObjectManager $manager): void
    {
        foreach ($this->getUserData() as $userData) {
            /** @var User $allUser */
            foreach ($this->allUsers as $allUser) {
                if (($allUser->getUsername() === $userData['username']) || ($allUser->getEmail() === $userData['username'])) {
                    continue 2;
                }
            }
            $user = new User();
            $user->setDisplayName($userData['displayname']);
            $user->setUsername($userData['username']);
            $user->setPassword($this->passwordHasher->hashPassword($user, $userData['password']));
            $user->setEmail($userData['email']);
            $user->setRoles($userData['roles']);
            $user->setLocale('en');
            $user->setBackendTheme('default');
            $user->setStatus($userData['status'] ?? UserStatus::DISABLED);

            $manager->persist($user);
            $this->addReference('user_' . $userData['username'], $user);
        }

        $manager->flush();
    }

    private function getCurrentUsers(): void
    {
        $this->allUsers = $this->users->findBy([], ['username' => 'ASC'], 100);

        /** @var User $user */
        foreach ($this->allUsers as $user) {
            $this->addReference('user_' . $user->getUsername(), $user);
        }
    }

    private function getUserData(): array
    {
        return [
            [
                'displayname' => 'Admin',
                'username' => 'admin',
                'password' => $this->append ? Str::generatePassword(12) : 'admin%1',
                'email' => 'admin@example.org',
                'roles' => ['ROLE_DEVELOPER', 'ROLE_WEBSERVICE'],
                'status' => UserStatus::ENABLED,
            ],
            [
                'displayname' => 'Crazy Steve',
                'username' => 'steve',
                'password' => Str::generatePassword(12),
                'email' => 'henkie@example.org',
                'roles' => ['ROLE_EDITOR', 'ROLE_EXTRA_1', 'ROLE_EXTRA_2', 'ROLE_USER_FRONTEND_GROUP1'],
                'status' => UserStatus::DISABLED,
            ],
            [
                'displayname' => 'Jane Doe',
                'username' => 'jane_chief',
                'password' => Str::generatePassword(12),
                'email' => 'jane_admin@example.org',
                'roles' => ['ROLE_CHIEF_EDITOR'],
                'status' => UserStatus::DISABLED,
            ],
            [
                'displayname' => 'Tom Doe',
                'username' => 'tom_admin',
                'password' => Str::generatePassword(12),
                'email' => 'tom_admin@example.org',
                'roles' => ['ROLE_ADMIN'],
                'status' => UserStatus::DISABLED,
            ],
            [
                'displayname' => 'John Doe',
                'username' => 'john_editor',
                'password' => Str::generatePassword(12),
                'email' => 'john_user@example.org',
                'roles' => ['ROLE_EDITOR'],
                'status' => UserStatus::DISABLED,
            ],
            [
                'displayname' => 'Eddie Enduser',
                'username' => 'eddie',
                'password' => Str::generatePassword(12),
                'email' => 'eddie@example.org',
                'roles' => ['ROLE_USER'],
                'status' => UserStatus::DISABLED,
            ],
        ];
    }
}
