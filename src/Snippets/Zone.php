<?php

declare(strict_types=1);

namespace Bolt\Snippets;

use Symfony\Component\HttpFoundation\Request;

/**
 * Zone constants class to define which part of the Bolt site that a request is
 * relative to.
 */
class Zone
{
    public const KEY = 'zone';

    public const FRONTEND = 'frontend';
    public const BACKEND = 'backend';
    public const ASYNC = 'async';

    public const EVERYWHERE = 'everywhere';
    public const NOWHERE = 'nowhere';

    /**
     * Check if request is for frontend routes.
     */
    public static function isFrontend(Request $request): bool
    {
        return static::is($request, static::FRONTEND);
    }

    /**
     * Check if request is for backend routes.
     */
    public static function isBackend(Request $request): bool
    {
        return static::is($request, static::BACKEND);
    }

    /**
     * Check if request is for asynchronous/AJAX routes.
     */
    public static function isAsync(Request $request): bool
    {
        return static::is($request, static::ASYNC);
    }

    /**
     * Check if request is for a specific zone.
     *
     * @param string $value
     */
    public static function is(Request $request, $value): bool
    {
        return static::get($request) === $value;
    }

    /**
     * Get the current zone.
     */
    public static function get(Request $request): ?string
    {
        return $request->attributes->get(static::KEY);
    }

    /**
     * Set the current zone.
     *
     * @param string $value
     */
    public static function set(Request $request, $value): void
    {
        $request->attributes->set(static::KEY, $value);
    }
}
