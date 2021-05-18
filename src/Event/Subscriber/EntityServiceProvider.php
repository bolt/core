<?php

declare(strict_types=1);

namespace Bolt\Event\Subscriber;

use Bolt\Entity\Field;
use Bolt\Entity\Field\SelectField;
use Bolt\Repository\FieldRepository;
use Bolt\Utils\Sanitiser;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\ConsoleEvents;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Twig\Environment;

/**
 * This class is after booting the Kernel, on KernelEvents::REQUEST and ConsoleEvents::COMMAND.
 * It provides services in classes that cannot be autowired.
 */
class EntityServiceProvider implements EventSubscriberInterface
{
    /** @var EntityManagerInterface */
    private $em;

    /** @var ContainerInterface */
    private $container;

    /** @var Sanitiser */
    private $sanitiser;

    /** @var Environment */
    private $twig;

    public function __construct(
        EntityManagerInterface $em,
        ContainerInterface $container,
        Sanitiser $sanitiser,
        Environment $twig)
    {
        $this->em = $em;
        $this->container = $container;
        $this->sanitiser = $sanitiser;
        $this->twig = $twig;
    }

    public function run(): void
    {
        // Add the entity manager as a static class property used in FieldRepository::factory()
        FieldRepository::setEntityManager($this->em);

        // Allow the SelectField to call services for dynamically populated values from services
        SelectField::setContainer($this->container);

        // Add the value sanitiser as a static class property used in Field::__toString()
        Field::setSanitiser($this->sanitiser);

        // Add the Twig Environment as a static class property used in Field::__toString()
        Field::setTwig($this->twig);
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::REQUEST => ['run'],
            ConsoleEvents::COMMAND => ['run'],
        ];
    }
}
