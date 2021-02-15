<?php

declare(strict_types=1);

/**
 * This file runs after `composer update` is run in bolt/project, in Bolt 4.2 and later.
 */
use Symplify\PackageBuilder\Console\Style\SymfonyStyleFactory;

require __DIR__ . '/../../../../../vendor/autoload.php';
require __DIR__ . '/run.php';

$symfonyStyleFactory = new SymfonyStyleFactory();
$symfonyStyle = $symfonyStyleFactory->create();

$symfonyStyle->note('Running composer "post-update-cmd" scripts');

// Install and copy the Bolt assets.
run('php bin/console assets:install --symlink --relative public', $symfonyStyle);
run('php bin/console bolt:copy-assets', $symfonyStyle);

// (Re-)configure Bolt extensions
run('php bin/console extensions:configure --with-config --ansi', $symfonyStyle);

// Clear cache, show Bolt info
run('php bin/console cache:clear --no-warmup', $symfonyStyle);
run('php bin/console bolt:info --ansi', $symfonyStyle, true);
