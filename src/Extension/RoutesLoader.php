<?php

declare(strict_types=1);

namespace Bolt\Extension;

use Symfony\Component\Config\Loader\Loader;
use Symfony\Component\Routing\RouteCollection;

class RoutesLoader extends Loader
{
    public function __construct(
        private readonly ExtensionRegistry $registry
    ) {
    }

    public function load(mixed $resource, ?string $type = null): RouteCollection
    {
        $routeCollection = new RouteCollection();

        foreach ($this->registry->getAllRoutes() as $name => $route) {
            $routeCollection->add($name, $route);
        }

        return $routeCollection;
    }

    public function supports(mixed $resource, $type = null): bool
    {
        return $type === 'bolt_extensions';
    }
}
