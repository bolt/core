<?php

declare(strict_types=1);

namespace Bolt\ComposerScripts;

class PostInstallScript extends Script
{
    public static function execute(): void
    {
        parent::init('Running composer "post-install-cmd" scripts');

        self::run('php bin/console bolt:copy-assets --ansi');
        self::run('php bin/console cache:clear --no-warmup --ansi');
        self::run('php bin/console assets:install --ansi');
        self::run('php bin/console extensions:configure --ansi');
    }
}
