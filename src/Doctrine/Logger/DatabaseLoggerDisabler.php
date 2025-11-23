<?php

namespace Bolt\Doctrine\Logger;

use Doctrine\Bundle\DoctrineBundle\Middleware\DebugMiddleware;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Driver\Middleware;
use Doctrine\DBAL\Logging\Middleware as LoggingMiddleware;

class DatabaseLoggerDisabler
{
    /**
     * Used to disable SQL logging to prevent memory issues.
     * See https://jolicode.com/blog/how-to-fix-memory-leak-in-doctrine-migrations.
     */
    public static function disableSqlLogger(Connection $connection): void
    {
        $configuration = $connection->getConfiguration();

        // Remove logging and debug middlewares from the configuration
        $filteredMiddlewares = array_filter(
            $configuration->getMiddlewares(),
            static fn (
                Middleware $middleware,
            ): bool => ! ($middleware instanceof LoggingMiddleware || $middleware instanceof DebugMiddleware),
        );

        // Update the configured middleware
        $configuration->setMiddlewares($filteredMiddlewares);
    }
}
