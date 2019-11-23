<?php

declare(strict_types=1);

namespace Bolt\Extension;

use Symfony\Component\Config\Loader\Loader;
use Symfony\Component\Routing\RouteCollection;

class RoutesLoader extends Loader
{
    /** @var ExtensionRegistry */
    private $registry;

    public function __construct(ExtensionRegistry $registry)
    {
        $this->registry = $registry;
    }

    public function load($resource, $type = null): RouteCollection
    {
        $routeCollection = new RouteCollection();

        foreach ($this->registry->getAllRoutes() as $name => $route) {
            $routeCollection->add($name, $route);
        }

        return $routeCollection;
    }

    public function supports($resource, $type = null): bool
    {
        return $type === 'bolt_extensions';
    }
}
