<?php

declare(strict_types=1);

namespace Bolt\ComposerScripts;

class PostUpdateScript extends Script
{
    public static function execute(): void
    {
        parent::init('Running composer "post-update-cmd" scripts');

        self::runPHP(['vendor/bolt/core/bin/fix-bundles.php']);
        self::runPHP(['vendor/bobdenotter/yaml-migrations/bin/yaml-migrate', 'process', '-c', 'vendor/bolt/core/yaml-migrations/config.yaml', '-v']);
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

        self::runConsole(['bolt:info', '--ansi']);
    }
}
