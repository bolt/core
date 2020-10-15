<?php

declare(strict_types=1);

namespace Bolt;

use ComposerPackages\Packages;

/**
 * Bolt's current version.
 *
 * @author Carson Full <carsonfull@gmail.com>
 */
final class Version
{
    /**
     * Bolt's version constant.
     *
     * This should take the form of:
     *   x.y.z [[alpha|beta|RC|patch] n]
     *
     * e.g. versions for:
     *   Stable — 3.0.0
     *   Development — 3.1.0 alpha 1
     */
    public const VERSION = '4.1.1';
    public const CODENAME = '';

    /**
     * Whether this release is a stable one.
     */
    public static function isStable(): bool
    {
        return (bool) preg_match('/^[0-9\.]+$/', static::VERSION);
    }

    /**
     * Compares a semantic version (x.y.z) against Bolt's version, given a
     * specified comparison operator.
     *
     * Note 1:
     * Be sure to include the `.z` number in the version given, as
     * omitting it can give inconsistent results.
     *
     * e.g. If the version of Bolt was '3.2.0' (or greater), then:
     *     `Version::compare('3.2', '>=');`
     * is NOT equal to, or greater than, Bolt's version.
     *
     * Note 2:
     * Pre-release versions, such as 3.2.0-beta1, are considered lower
     * than their final release counterparts (like 2.3.0). As you may notice,
     * the difference being that Bolt '3.2.0-beta1' is considered LOWER than
     * the `compare($version)` value of '3.2.0'.
     *
     * e.g. If the version of Bolt was '3.2.0 beta 1', then:
     *     `Version::compare('3.2.0', '>=');`
     * is equal to, or greater than, Bolt's version.
     *
     * @see http://semver.org/ For an explanation on semantic versioning.
     * @see http://php.net/manual/en/function.version-compare.php#refsect1-function.version-compare-notes Notes on version_compare
     *
     * @param string $version the version to compare
     * @param string $operator the comparison operator: <, <=, >, >=, ==, !=
     *
     * @return bool whether the comparison succeeded
     */
    public static function compare($version, $operator): bool
    {
        $currentVersion = str_replace(' ', '', mb_strtolower(static::VERSION));
        $version = str_replace(' ', '', mb_strtolower($version));

        return version_compare($version, $currentVersion, $operator);
    }

    /**
     * Returns a version formatted for composer.
     */
    public static function forComposer(): string
    {
        if (mb_strpos(static::VERSION, ' ') === false) {
            return static::VERSION;
        }

        $version = explode(' ', static::VERSION, 2);

        return $version[0];
    }

    public static function fullName(): string
    {
        return static::VERSION;
    }

    public static function codeName(): string
    {
        return static::CODENAME;
    }

    public static function name(): ?string
    {
        if (mb_strpos(static::VERSION, ' ') === false) {
            return null;
        }

        return explode(' ', static::VERSION)[1];
    }

    /**
     * Determine the "install type": Whether we're currently running a direct
     * git clone, a composer install project, or a packaged distribution.
     */
    public static function installType(): string
    {
        $type = 'Git clone';

        // If we're currently in a `vendor` folder, we're not a direct Git clone
        if (in_array('vendor', explode(DIRECTORY_SEPARATOR, __DIR__), true)) {
            $type = 'Composer install';

            // If the `tests/` folder is removed, it's probably a "packaged distro", like `.tgz` or `.zip`
            if (! file_exists(dirname(__DIR__) . DIRECTORY_SEPARATOR . 'tests')) {
                $type = 'Packaged distribution';
            }
        }

        return $type;
    }

    public static function getSymfonyVersion(): string
    {
        return Packages::symfonyFrameworkBundle()->getPrettyVersion();
    }
}
