<?php

declare(strict_types=1);

namespace Bolt\Command;

use Bolt\Common\Str;
use Bolt\Entity\User;
use Bolt\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Resets password for the user with the specified username.
 */
class ResetPasswordCommand extends Command
{
    /** @var string */
    protected static $defaultName = 'bolt:reset-password';

    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly UserPasswordHasherInterface $passwordHasher,
        private readonly UserRepository $userRepository,
        private readonly ValidatorInterface $validator
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Reset password for a user.')
            ->addArgument('username', InputArgument::REQUIRED, 'Username of the user')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $username = $input->getArgument('username');

        /** @var User|null $user */
        $user = $this->userRepository->findOneBy(['username' => $username]);

        if ($user === null) {
            $io->error(sprintf('No user found with username: %s', $username));

            return Command::FAILURE;
        }

        $io->note(sprintf('Changing password for user: %s', $username));

        $passwordQuestion = new Question('Password (input is hidden)', Str::generatePassword());
        $passwordQuestion->setHidden(true);
        $passwordQuestion->setValidator(function (?string $password): ?string {
            $errors = $this->validator->validatePropertyValue(User::class, 'plainPassword', $password);
            if ($errors->count() > 0) {
                throw new InvalidArgumentException($errors->get(0)->getMessage());
            }

            return $password;
        });

        $plainPassword = $io->askQuestion($passwordQuestion);

        $user->setPlainPassword($plainPassword);

        // See https://symfony.com/doc/current/book/security.html#security-encoding-password
        $hashedPassword = $this->passwordHasher->hashPassword($user, $user->getPlainPassword());
        $user->setPassword($hashedPassword);
        $user->eraseCredentials();

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $io->success('Updated password.');

        return Command::SUCCESS;
    }
}
