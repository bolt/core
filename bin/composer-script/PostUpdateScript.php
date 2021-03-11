<?php

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
        self::run('php bin/console doctrine:migrations:up-to-date --ansi');
        self::run('php bin/console bolt:info --ansi');
    }
}
