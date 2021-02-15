<?php

declare(strict_types=1);

// this script makes sure the install scripts are not required for composer update in CI
// @see https://github.com/bolt/core/pull/1918#issuecomment-701460769

use OndraM\CiDetector\CiDetector;
use Symplify\PackageBuilder\Console\Style\SymfonyStyleFactory;

require __DIR__ . '/../../vendor/autoload.php';

$symfonyStyleFactory = new SymfonyStyleFactory();
$symfonyStyle = $symfonyStyleFactory->create();

$ciDetector = new CiDetector();
if ($ciDetector->isCiDetected()) {
    $symfonyStyle->warning(sprintf('"php %s" skipped in CI composer', __FILE__));

    return;
}

$symfonyStyle->note('Running composer "post-update-cmd" scripts');

exec('php bin/console extensions:configure --ansi');

// @auto-scripts
exec('php bin/console cache:clear --no-warmup');
exec('php bin/console assets:install --symlink --relative public');

exec('php bin/console bolt:info --ansi');
