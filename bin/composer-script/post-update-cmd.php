<?php

declare(strict_types=1);

// this script makes sure the install scripts are not required for composer update in CI
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

$symfonyStyle->note('Running composer "post-update-cmd" scripts');

run('php bin/console extensions:configure --with-config --ansi', $symfonyStyle);

// @auto-scripts
run('php bin/console cache:clear --no-warmup', $symfonyStyle);
run('php bin/console assets:install --symlink --relative public', $symfonyStyle);

$migrate = 'Database is out-of-date. To update the database, run `php bin/console doctrine:migrations:migrate`.';
$migrate .= ' You are strongly advised to backup your database before migrating.';
run('php bin/console doctrine:migrations:up-to-date', $symfonyStyle, false, $migrate);

run('php bin/console bolt:info --ansi', $symfonyStyle, true);
