<?php

declare(strict_types=1);

require __DIR__.'/vendor/autoload.php';

$kernel = new \Bolt\Kernel('test', true);
$kernel->boot();

$application = new \Symfony\Bundle\FrameworkBundle\Console\Application($kernel);
$application->setAutoExit(false);
$application->setCatchExceptions(false);

$application->run(new \Symfony\Component\Console\Input\StringInput('doctrine:database:drop --force --quiet'));
$application->run(new \Symfony\Component\Console\Input\StringInput('doctrine:database:create --quiet'));
$application->run(new \Symfony\Component\Console\Input\StringInput('doctrine:schema:create --quiet'));

$kernel->shutdown();
