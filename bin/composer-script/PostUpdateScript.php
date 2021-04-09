<?php

declare(strict_types=1);

namespace Bolt\ComposerScripts;

class PostUpdateScript extends Script
{
    public static function execute(): void
    {
        parent::init('Running composer "post-update-cmd" scripts');

        self::run('php vendor/bobdenotter/yaml-migrations/bin/yaml-migrate process -c vendor/bolt/core/yaml-migrations/config.yaml -v');
        self::run('php bin/console extensions:configure --with-config --ansi');
        self::run('php bin/console cache:clear --no-warmup --ansi');
        self::run('php bin/console assets:install --symlink --relative public --ansi');

        // Only run, if the tables are initialised already, _and_ Doctrine thinks we need to
        $migrationError = ! self::run('php bin/console bolt:info --tablesInitialised') &&
            self::run('php bin/console doctrine:migrations:up-to-date --ansi');

        if ($migrationError) {
            self::$console->warning('Please run `php bin/console doctrine:migrations:migrate` to execute the database migrations.');
        }

        self::run('php bin/console bolt:info --ansi');
    }
}
