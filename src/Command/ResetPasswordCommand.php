<?php

namespace App\Command;

use Bolt\Common\Str;
use Bolt\Entity\User;
use Bolt\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Resets password for the user with the specified username.
 *
 * @package App\Command
 */
class ResetPasswordCommand extends Command
{
    protected static $defaultName = 'bolt:reset-password';

    /** @var EntityManagerInterface */
    private $entityManager;

    /** @var UserPasswordEncoderInterface */
    private $passwordEncoder;

    /** @var ValidatorInterface */
    private $validator;

    /** @var UserRepository */
    private $userRepository;

    public function __construct(EntityManagerInterface $em, UserPasswordEncoderInterface $encoder,
                                UserRepository $userRepository, ValidatorInterface $validator)
    {
        parent::__construct();

        $this->entityManager = $em;
        $this->passwordEncoder = $encoder;
        $this->validator = $validator;
        $this->userRepository = $userRepository;
    }

    protected function configure()
    {
        $this
            ->setDescription('Reset password for a user.')
            ->addArgument('username', InputArgument::REQUIRED, 'User name')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $username = $input->getArgument('username');

        /** @var User $user */
        $user = $this->userRepository->findOneBy(['username' => $username]);

        if (!$user) {
            $io->error(sprintf('No user found with username: %s', $username));
            return Command::FAILURE;
        }

        $io->note(sprintf('Changing password for user: %s', $username));

        $passwordQuestion = new Question('Password (input is hidden)', Str::generatePassword());
        $passwordQuestion->setHidden(true);
        $passwordQuestion->setValidator(function (?string $password) {
            $errors = $this->validator->validatePropertyValue(User::class, 'plainPassword', $password);
            if ($errors->count() > 0) {
                throw new InvalidArgumentException($errors->get(0)->getMessage());
            }

            return $password;
        });

        $plainPassword = $io->askQuestion($passwordQuestion);

        $user->setPlainPassword($plainPassword);

        // See https://symfony.com/doc/current/book/security.html#security-encoding-password
        $encodedPassword = $this->passwordEncoder->encodePassword($user, $user->getPlainPassword());
        $user->setPassword($encodedPassword);
        $user->eraseCredentials();

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $io->success('Updated password.');

        return Command::SUCCESS;
    }
}
