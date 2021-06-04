<?php

declare(strict_types=1);

// this script makes sure the install scripts are not required for composer update in CI
// @see https://github.com/bolt/core/pull/1918#issuecomment-701460769

use Bolt\ComposerScripts\Script;
use OndraM\CiDetector\CiDetector;

require __DIR__ . '/../../vendor/autoload.php';
require __DIR__ . '/Script.php';

$symfonyStyle = Script::createSymfonyStyle();

$ciDetector = new CiDetector();
if ($ciDetector->isCiDetected()) {
    $symfonyStyle->warning(sprintf('"php %s" skipped in CI composer', __FILE__));

    return;
}

$symfonyStyle->note('Running composer "post-update-cmd" scripts');

Script::run('php bin/console extensions:configure --with-config --ansi');

// @auto-scripts
Script::run('php bin/console cache:clear --no-warmup');
Script::run('php bin/console assets:install --symlink --relative public');

$res = Script::run('php bin/console doctrine:migrations:up-to-date');

if (! $res) {
    $migrate = 'Database is out-of-date. To update the database, run `php bin/console doctrine:migrations:migrate`.';
    $migrate .= ' You are strongly advised to backup your database before migrating.';

    $symfonyStyle->warning($migrate);
}

Script::run('php bin/console bolt:info --ansi');
