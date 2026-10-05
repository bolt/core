<?php

declare(strict_types=1);

namespace Bolt\Tests\Controller;

use Bolt\Canonical;
use Bolt\Configuration\Config;
use Bolt\Controller\TwigAwareController;
use Bolt\TemplateChooser;
use Bolt\Twig\CommonExtension;
use Bolt\Utils\Sanitiser;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Symfony\Component\Asset\Packages;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Twig\Environment;

class TwigAwareControllerTest extends TestCase
{
    /**
     * Extensions' controllers are registered by the generated
     * config/services_bolt.yaml: autowired and autoconfigured, but without the
     * binds of the project's config/services.yaml, such as `$defaultLocale`.
     */
    public function testCanBeAutowiredWithoutTheProjectBinds(): void
    {
        $container = new ContainerBuilder();
        $container->setParameter('locale', 'nl');

        $services = [
            ContainerInterface::class,
            Config::class,
            Environment::class,
            Packages::class,
            Canonical::class,
            Sanitiser::class,
            TemplateChooser::class,
            CommonExtension::class,
        ];
        foreach ($services as $service) {
            $container->register($service)
                ->setSynthetic(true);
        }

        $container->register(TwigAwareController::class, TwigAwareController::class)
            ->setAutowired(true)
            ->setAutoconfigured(true)
            ->setPublic(true);

        $container->compile();

        $arguments = [];
        foreach ($container->getDefinition(TwigAwareController::class)->getMethodCalls() as [$method, $methodArguments]) {
            $arguments[$method] = $methodArguments;
        }

        $this->assertSame('nl', $arguments['setAutowire'][6] ?? null);
    }
}
