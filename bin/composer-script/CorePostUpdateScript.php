<?php

declare(strict_types=1);

namespace Bolt\ComposerScripts;

use OndraM\CiDetector\CiDetector;

class CorePostUpdateScript extends Script
{
    public static function execute(): void
    {
        $symfonyStyle = self::createSymfonyStyle();

        $ciDetector = new CiDetector();
        if ($ciDetector->isCiDetected()) {
            $symfonyStyle->warning(sprintf('"php %s" skipped in CI composer', __FILE__));

            return;
        }

        parent::init('Running composer "post-update-cmd" scripts, for `bolt/core` installation');

        self::runConsole(['extensions:configure', '--with-config', '--ansi']);
        self::runConsole(['cache:clear', '--no-warmup']);
        self::runConsole(['assets:install', '--symlink', '--relative', 'public']);
        self::runConsole(['doctrine:migrations:migrate', '--no-interaction']);

        $res = self::runConsole(['doctrine:migrations:up-to-date']);

        if ($res) {
            $migrate = 'Database is out-of-date. To update the database, run `php bin/console doctrine:migrations:migrate`.';
            $migrate .= ' You are strongly advised to backup your database before migrating.';

            $symfonyStyle->warning($migrate);
        }

        self::runConsole(['bolt:info', '--ansi']);
    }
}
