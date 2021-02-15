<?php

declare(strict_types=1);

/**
 * This file runs after `composer install` is run in bolt/project, in Bolt 4.2 and later.
 */
use Symplify\PackageBuilder\Console\Style\SymfonyStyleFactory;

require __DIR__ . '/../../../../../vendor/autoload.php';
require __DIR__ . '/run.php';

$symfonyStyleFactory = new SymfonyStyleFactory();
$symfonyStyle = $symfonyStyleFactory->create();

// Run the auto scripts.
run('php bin/console bolt:copy-assets --ansi', $symfonyStyle);
run('php bin/console cache:clear --no-warmup --ansi', $symfonyStyle);
run('php bin/console assets:install --ansi', $symfonyStyle);

// Configure the extensions
run('php bin/console extensions:configure --ansi', $symfonyStyle);
