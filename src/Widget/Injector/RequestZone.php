<?php

declare(strict_types=1);

namespace Bolt\Widget\Injector;

use Symfony\Component\HttpFoundation\Request;

/**
 * Zone constants class to define which part of the Bolt site that a request is
 * relative to.
 */
class RequestZone
{
    protected const KEY = 'zone';

    public const FRONTEND = 'frontend';
    public const BACKEND = 'backend';
    public const ASYNC = 'async';

    public const EVERYWHERE = 'everywhere';
    public const NOWHERE = 'nowhere';
    public const ERROR = 'error';

    /**
     * Check if request is for frontend routes.
     */
    public static function isForFrontend(Request $request): bool
    {
        return static::is($request, static::FRONTEND);
    }

    /**
     * Check if request is for backend routes.
     */
    public static function isForBackend(Request $request): bool
    {
        return static::is($request, static::BACKEND);
    }

    /**
     * Check if request is for asynchronous/AJAX routes.
     */
    public static function isForAsync(Request $request): bool
    {
        return static::is($request, static::ASYNC);
    }

    /**
     * Check if request is for handling an exception/error.
     */
    public static function isForError(Request $request): bool
    {
        return static::is($request, static::ERROR);
    }

    /**
     * Check if request is for a specific zone.
     */
    public static function is(Request $request, string $value): bool
    {
        return static::getFromRequest($request) === $value;
    }

    /**
     * Get the current zone.
     */
    public static function getFromRequest(?Request $request): string
    {
        return $request->attributes->get(static::KEY) ?: static::NOWHERE;
    }

    /**
     * Set the current zone.
     */
    public static function setToRequest(Request $request, string $value): void
    {
        $request->attributes->set(static::KEY, $value);
    }
}
