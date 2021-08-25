<?php

declare(strict_types=1);

namespace Bolt\Command;

use Bolt\Common\Str;
use Bolt\Configuration\Config;
use Bolt\Entity\User;
use Bolt\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Exception\RuntimeException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Stopwatch\Stopwatch;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * A console command that creates users and stores them in the database.
 *
 * To use this command, open a terminal window, enter into your project
 * directory and execute the following:
 *
 *     $ php bin/console app:add-user
 *
 * To output detailed information, increase the command verbosity:
 *
 *     $ php bin/console app:add-user -vv
 *
 * See https://symfony.com/doc/current/cookbook/console/console_command.html
 * For more advanced uses, commands can be defined as services too. See
 * https://symfony.com/doc/current/console/commands_as_services.html
 *
 * @author Javier Eguiluz <javier.eguiluz@gmail.com>
 * @author Yonel Ceruto <yonelceruto@gmail.com>
 */
class AddUserCommand extends Command
{
    /**
     * to make your command lazily loaded, configure the $defaultName static property,
     * so it will be instantiated only when the command is actually called.
     */
    protected static $defaultName = 'bolt:add-user';

    /** @var SymfonyStyle */
    private $io;

    /** @var EntityManagerInterface */
    private $entityManager;

    /** @var UserPasswordEncoderInterface */
    private $passwordEncoder;

    /** @var ValidatorInterface */
    private $validator;

    /** @var Config */
    private $config;

    public function __construct(EntityManagerInterface $em, UserPasswordEncoderInterface $encoder, ValidatorInterface $validator, Config $config)
    {
        parent::__construct();

        $this->entityManager = $em;
        $this->passwordEncoder = $encoder;
        $this->validator = $validator;
        $this->config = $config;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure(): void
    {
        $this
            ->setDescription('Creates users and stores them in the database')
            ->setHelp($this->getCommandHelp())
            // commands can optionally define arguments and/or options (mandatory and optional)
            // see https://symfony.com/doc/current/components/console/console_arguments.html
            ->addArgument('username', InputArgument::OPTIONAL, 'The username of the new user')
            ->addArgument('password', InputArgument::OPTIONAL, 'The plain password of the new user')
            ->addArgument('email', InputArgument::OPTIONAL, 'The email of the new user')
            ->addArgument('display-name', InputArgument::OPTIONAL, 'The display name of the new user')
            ->addOption('admin', null, InputOption::VALUE_NONE, 'If set, the user is created as an administrator (shortcut for `--roles=ROLE_ADMIN`)')
            ->addOption('developer', null, InputOption::VALUE_NONE, 'If set, the user is created as a developer (shortcut for `--roles=ROLE_DEVELOPER`)')
            ->addOption('roles', null, InputOption::VALUE_IS_ARRAY | InputOption::VALUE_OPTIONAL, 'If set, provide a list of roles that the new user will be assigned');
    }

    /**
     * This optional method is the first one executed for a command after configure()
     * and is useful to initialize properties based on the input arguments and options.
     */
    protected function initialize(InputInterface $input, OutputInterface $output): void
    {
        // SymfonyStyle is an optional feature that Symfony provides so you can
        // apply a consistent look to the commands of your application.
        // See https://symfony.com/doc/current/console/style.html
        $this->io = new SymfonyStyle($input, $output);
    }

    /**
     * This method is executed after initialize() and before execute(). Its purpose
     * is to check if some of the options/arguments are missing and interactively
     * ask the user for those values.
     *
     * This method is completely optional. If you are developing an internal console
     * command, you probably should not implement this method because it requires
     * quite a lot of work. However, if the command is meant to be used by external
     * users, this method is a nice way to fall back and prevent errors.
     */
    protected function interact(InputInterface $input, OutputInterface $output): void
    {
        if ($input->getArgument('username') !== null && $input->getArgument('password') !== null && $input->getArgument('email') !== null && $input->getArgument('display-name') !== null) {
            return;
        }

        $this->io->title('Add Bolt User Command');
        $this->io->text([
            'If you prefer to not use this interactive wizard, provide the',
            'arguments required by this command as follows:',
            '',
            ' $ php bin/console bolt:add-user username password email@example.com DisplayName',
            '',
            'Now we\'ll ask you for the value of all the missing command arguments.',
        ]);

        // Ask for the username if it's not defined
        $username = $input->getArgument('username');
        if ($username !== null) {
            $this->io->text(' > <info>Username</info>: ' . $username);
        } else {
            $username = $this->io->ask('Username', null, function (?string $username) {
                $errors = $this->validator->validatePropertyValue(User::class, 'username', $username);
                if ($errors->count() > 0) {
                    throw new InvalidArgumentException($errors->get(0)->getMessage());
                }

                return $username;
            });
            $input->setArgument('username', $username);
        }

        // Ask for the password if it's not defined
        $password = $input->getArgument('password');
        if ($password !== null) {
            $this->io->text(' > <info>Password</info>: ' . str_repeat('*', mb_strlen($password)));
        } else {
            $passwordQuestion = new Question('Password (input is hidden)', Str::generatePassword());
            $passwordQuestion->setHidden(true);
            $passwordQuestion->setValidator(function (?string $password) {
                $errors = $this->validator->validatePropertyValue(User::class, 'plainPassword', $password);
                if ($errors->count() > 0) {
                    throw new InvalidArgumentException($errors->get(0)->getMessage());
                }

                return $password;
            });

            $password = $this->io->askQuestion($passwordQuestion);
            $input->setArgument('password', $password);
        }

        // Ask for the email if it's not defined
        $email = $input->getArgument('email');
        if ($email !== null) {
            $this->io->text(' > <info>Email</info>: ' . $email);
        } else {
            $email = $this->io->ask('Email', null, function (?string $email) {
                $errors = $this->validator->validatePropertyValue(User::class, 'email', $email);
                if ($errors->count() > 0) {
                    throw new InvalidArgumentException($errors->get(0)->getMessage());
                }

                return $email;
            });
            $input->setArgument('email', $email);
        }

        // Ask for the display name if it's not defined
        $displayName = $input->getArgument('display-name');
        if ($displayName !== null) {
            $this->io->text(' > <info>Display Name</info>: ' . $displayName);
        } else {
            $displayName = $this->io->ask('Display Name', null, function (?string $displayName) {
                $errors = $this->validator->validatePropertyValue(User::class, 'displayName', $displayName);
                if ($errors->count() > 0) {
                    throw new InvalidArgumentException($errors->get(0)->getMessage());
                }

                return $displayName;
            });
            $input->setArgument('display-name', $displayName);
        }

        // Set the roles for the new user.
        $assignableRoles = $this->config->get('permissions/assignable_roles')->toArray();

        $isAdmin = $input->getOption('admin') ? ['ROLE_ADMIN'] : [];
        $isDeveloper = $input->getOption('developer') ? ['ROLE_DEVELOPER'] : [];
        $roles = $input->getOption('roles') ?? [];
        $roles = array_merge($roles, $isAdmin, $isDeveloper);

        $nonAssignableRoles = array_diff($roles, $assignableRoles);

        // Only allow the assignable roles to be assigned.
        $roles = array_intersect($assignableRoles, $roles);

        if (! empty($nonAssignableRoles)) {
            $this->io->warning(sprintf('The role(s) [%s] are non-assignable and will be ignored. Please check your config/permissions.yaml file.', implode(',', $nonAssignableRoles)));
        }

        if (empty($roles)) {
            // Ask the roles question, if neither --roles nor --admin is set.
            $rolesQuestion = new ChoiceQuestion('Role', $assignableRoles);
            $roles = [$this->io->askQuestion($rolesQuestion)];
        }

        $input->setOption('roles', $roles);
    }

    /**
     * This method is executed after interact() and initialize(). It usually
     * contains the logic to execute to complete this command task.
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $stopwatch = new Stopwatch();
        $stopwatch->start('add-user-command');

        $username = $input->getArgument('username');
        $plainPassword = $input->getArgument('password');
        $email = $input->getArgument('email');
        $displayName = $input->getArgument('display-name');
        $roles = $input->getOption('roles');

        // create the user and encode its password
        $user = UserRepository::factory($displayName, $username, $email);
        $user->setRoles($roles);
        $user->setLocale('en');
        $user->setBackendTheme('default');
        $user->setPlainPassword($plainPassword);

        $errors = $this->validator->validate($user);
        if ($errors->count() > 0) {
            $errorMessages = [];
            /** @var ConstraintViolationInterface $error */
            foreach ($errors as $error) {
                $errorMessages[] = $error->getMessage();
            }

            throw new RuntimeException(implode(', ', $errorMessages));
        }

        // See https://symfony.com/doc/current/book/security.html#security-encoding-password
        $encodedPassword = $this->passwordEncoder->encodePassword($user, $user->getPlainPassword());
        $user->setPassword($encodedPassword);
        $user->eraseCredentials();

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $this->io->success(sprintf('User was successfully created: %s (%s) [%s]', $user->getUsername(), $user->getEmail(), implode(',', $user->getRoles())));

        $event = $stopwatch->stop('add-user-command');
        if ($output->isVerbose()) {
            $this->io->comment(sprintf('New user database id: %d / Elapsed time: %.2f ms / Consumed memory: %.2f MB', $user->getId(), $event->getDuration(), $event->getMemory() / (1024 ** 2)));
        }

        return Command::SUCCESS;
    }

    /**
     * The command help is usually included in the configure() method, but when
     * it's too long, it's better to define a separate method to maintain the
     * code readability.
     */
    private function getCommandHelp(): string
    {
        return <<<'HELP'
The <info>%command.name%</info> command creates new users and saves them in the database:

  <info>php %command.full_name%</info> <comment>username password email</comment>

By default the command creates regular users. To create administrator users,
add the <comment>--admin</comment> option:

  <info>php %command.full_name%</info> username password email <comment>--admin</comment>

If you omit any of the three required arguments, the command will ask you to
provide the missing values:

  # command will ask you for the email
  <info>php %command.full_name%</info> <comment>username password</comment>

  # command will ask you for the email and password
  <info>php %command.full_name%</info> <comment>username</comment>

  # command will ask you for all arguments
  <info>php %command.full_name%</info>

HELP;
    }
}
