<?php

declare(strict_types=1);

namespace Bolt\ComposerScripts;

class PostUpdateScript extends Script
{
    public static function execute(): void
    {
        parent::init('Running composer "post-update-cmd" scripts');

        self::run('php bin/console extensions:configure --with-config --ansi');
        self::run('php bin/console cache:clear --no-warmup --ansi');
        self::run('php bin/console assets:install --symlink --relative public --ansi');
        self::run('php bin/console bolt:info --ansi');
    }
}
