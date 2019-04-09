<?php

declare(strict_types=1);

namespace Bolt\Snippet;

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
     */
    public static function is(Request $request, string $value): bool
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
     */
    public static function set(Request $request, string $value): void
    {
        $request->attributes->set(static::KEY, $value);
    }
}
