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

// Check for database migrations
$migrate = 'Database is out-of-date. To update the database, run `php bin/console doctrine:migrations:migrate`.';
$migrate .= ' You are strongly advised to backup your database before migrating.';
run('php bin/console doctrine:migrations:up-to-date', $symfonyStyle, false, $migrate);

// Configure the extensions
run('php bin/console extensions:configure --ansi', $symfonyStyle);
