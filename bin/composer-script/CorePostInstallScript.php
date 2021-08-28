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

        self::run('php bin/console extensions:configure --with-config --ansi');
        self::run('php bin/console cache:clear --no-warmup');
        self::run('php bin/console assets:install --symlink --relative public');
        self::run('php bin/console bolt:info --ansi');
    }
}
