<?php

declare(strict_types=1);

namespace Bolt\ComposerScripts;

class PostInstallScript extends Script
{
    public static function execute(): void
    {
        parent::init('Running composer "post-install-cmd" scripts');

        self::runConsole(['cache:clear', '--no-warmup', '--ansi']);
        self::runConsole(['assets:install', '--symlink', '--relative', 'public', '--ansi']);
        self::runConsole(['bolt:copy-assets', '--ansi']);
        self::runConsole(['extensions:configure', '--with-config', '--ansi']);

        // Only run, if the tables are initialised already, _and_ Doctrine thinks we need to
        $migrationError = ! self::runConsole(['bolt:info', '--tablesInitialised']) &&
            self::runConsole(['doctrine:migrations:up-to-date', '--ansi']);

        if ($migrationError) {
            self::$console->warning('Please run `php bin/console doctrine:migrations:migrate` to execute the database migrations.');
        }
    }
}
