<?php

/**
 * This file runs when `composer update` is run in bolt/project, in Bolt 4.2 and later.
 */
use Symplify\PackageBuilder\Console\Style\SymfonyStyleFactory;

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/run.php';

$symfonyStyleFactory = new SymfonyStyleFactory();
$symfonyStyle = $symfonyStyleFactory->create();

$symfonyStyle->note('Running composer "post-update-cmd" scripts');

// Run yaml migrations
run('php bin/yaml-migrate process -c config.yaml -v', $symfonyStyle);

// Install and copy the Bolt assets.
run('php bin/console assets:install --symlink --relative %PUBLIC_DIR%', $symfonyStyle);
run('php bin/console bolt:copy-assets', $symfonyStyle);

// Configure Bolt extensions
run('php bin/console extensions:configure --with-config --ansi', $symfonyStyle);

// Check for database migrations
$migrate = "Database is out-of-date. To update the database, run `php bin/console doctrine:migrations:migrate`.";
$migrate .= " You are strongly advised to backup your database before migrating.";
run('php bin/console doctrine:migrations:up-to-date', $symfonyStyle, false, $migrate);

// @auto-scripts
run('php bin/console cache:clear --no-warmup', $symfonyStyle);
run('php bin/console bolt:info --ansi', $symfonyStyle);
