<?php

// this script makes sure the install scripts are not required for composer install in CI
// @see https://github.com/bolt/core/pull/1918#issuecomment-701460769

use OndraM\CiDetector\CiDetector;
use Symplify\PackageBuilder\Console\Style\SymfonyStyleFactory;

require __DIR__ . '/../../vendor/autoload.php';
require __DIR__ . '/run.php';

$symfonyStyleFactory = new SymfonyStyleFactory();
$symfonyStyle = $symfonyStyleFactory->create();

$ciDetector = new CiDetector();
if ($ciDetector->isCiDetected()) {
    $symfonyStyle->warning(sprintf('"php %s" skipped in CI composer', __FILE__));
    return;
}

$symfonyStyle->note('Running composer "post-install-cmd" scripts');

run('php bin/console extensions:configure --with-config --ansi', $symfonyStyle);

// @auto-scripts
run('php bin/console cache:clear --no-warmup', $symfonyStyle);
run('php bin/console assets:install --symlink --relative public', $symfonyStyle);

run('php bin/console bolt:info --ansi', $symfonyStyle, true);
