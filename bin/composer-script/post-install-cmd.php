<?php

declare(strict_types=1);

// this script makes sure the install scripts are not required for composer install in CI
// @see https://github.com/bolt/core/pull/1918#issuecomment-701460769

use OndraM\CiDetector\CiDetector;
use Bolt\ComposerScripts\Script;

require __DIR__ . '/../../vendor/autoload.php';
require __DIR__ . '/Script.php';

$symfonyStyle = Script::createSymfonyStyle();

$ciDetector = new CiDetector();
if ($ciDetector->isCiDetected()) {
    $symfonyStyle->warning(sprintf('"php %s" skipped in CI composer', __FILE__));

    return;
}

$symfonyStyle->note('Running composer "post-install-cmd" scripts');

Script::run('php bin/console extensions:configure --with-config --ansi');

// @auto-scripts
Script::run('php bin/console cache:clear --no-warmup');
Script::run('php bin/console assets:install --symlink --relative public');

Script::run('php bin/console bolt:info --ansi');
