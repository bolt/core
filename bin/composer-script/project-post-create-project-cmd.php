<?php

/**
 * This file runs when `composer create-project` is run in bolt/project, in Bolt 4.2 and later.
 */
use Symplify\PackageBuilder\Console\Style\SymfonyStyleFactory;

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/run.php';

$symfonyStyleFactory = new SymfonyStyleFactory();
$symfonyStyle = $symfonyStyleFactory->create();

$symfonyStyle->note('Running composer "post-create-project-cmd" scripts');

// Run bolt/project post-create-project script
run('php bin/post-create-project.php', $symfonyStyle);

// Copy the default themes
run('php bin/console bolt:copy-themes', $symfonyStyle);

// Run the welcome command
run('php bin/console bolt:welcome', $symfonyStyle);
