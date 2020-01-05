<?php

declare(strict_types=1);

namespace Bolt\Extension;

use Bolt\Configuration\Config;
use Bolt\Widgets;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Container\ContainerInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Stopwatch\Stopwatch;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Twig\Environment;

trait ServicesTrait
{
    /** @var EntityManagerInterface */
    protected $entityManager;

    /** @var ContainerInterface */
    protected $container;

    /**
     * Injects commonly used objects into the extension, for use by the
     * extension. Called from the listener.
     *
     * @see ExtensionSubscriber
     */
    public function injectObjects(array $objects): void
    {
        $this->entityManager = $objects['manager'];
        $this->container = $objects['container'];
    }

    /**
     * This bit of code allows us to get services from the container, even if
     * they are not marked public. We need to be able to do this, because we
     * can't anticipate which services an extension's author will want to get,
     * and neither should we want to make them all public. So, we resort to
     * this, regardless of them being private / public. With great power comes
     * great responsibility.
     *
     * Note: We wouldn't have to do this, if we could Autowire services in our
     * own code. If you have good ideas on how to accomplish that, we'd be
     * happy to hear from your ideas.
     *
     * @throws \ReflectionException
     */
    public function getService(string $name)
    {
        $container = $this->getContainer();

        if ($container->has($name)) {
            return $container->get($name);
        }

        $reflectedContainer = new \ReflectionClass($container);
        $reflectionProperty = $reflectedContainer->getProperty('privates');
        $reflectionProperty->setAccessible(true);

        $privateServices = $reflectionProperty->getValue($container);

        if (array_key_exists($name, $privateServices)) {
            return $privateServices[$name];
        }

        return null;
    }

    public function getWidgets(): ?Widgets
    {
        return $this->getService(\Bolt\Widgets::class);
    }

    public function getBoltConfig(): Config
    {
        return $this->getService(\Bolt\Configuration\Config::class);
    }

    public function getTwig(): Environment
    {
        return $this->getService('twig');
    }

    public function getSession(): Session
    {
        return $this->getService('session');
    }

    public function getEventDispatcher(): EventDispatcherInterface
    {
        return $this->getService('event_dispatcher');
    }

    public function getObjectManager(): ObjectManager
    {
        return $this->entityManager;
    }

    public function getStopwatch(): Stopwatch
    {
        return $this->getService('debug.stopwatch');
    }

    public function getContainer(): ContainerInterface
    {
        return $this->container;
    }
}
