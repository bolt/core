<?php

declare(strict_types=1);

namespace Bolt\ComposerScripts;

use OndraM\CiDetector\CiDetector;

class CorePostInstallScript extends Script
{
    public static function execute(): void
    {
        $symfonyStyle = self::createSymfonyStyle();

        $ciDetector = new CiDetector();
        if ($ciDetector->isCiDetected()) {
            $symfonyStyle->warning(sprintf('"php %s" skipped in CI composer', __FILE__));

            return;
        }

        parent::init('Running composer "post-install-cmd" scripts, for `bolt/core` installation');

        self::runConsole(['extensions:configure', '--with-config', '--ansi']);
        self::runConsole(['cache:clear', '--no-warmup']);
        self::runConsole(['assets:install', '--symlink', '--relative', 'public']);
        self::runConsole(['bolt:info', '--ansi']);
    }
}
