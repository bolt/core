<?php

declare(strict_types=1);

namespace Bolt\Tests;

use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Console\Input\StringInput;

class DbAwareTestCase extends WebTestCase
{
    /** @var  Application $application */
    private static $application;

    /** @var  EntityManager $entityManager */
    private $entityManager;

    protected function getEm(): EntityManager
    {
        return $this->entityManager;
    }

    protected function setUp()
    {
        self::runCommand('doctrine:database:drop --force');
        self::runCommand('doctrine:database:create');
        self::runCommand('doctrine:schema:create');
        self::runCommand('doctrine:fixtures:load --no-interaction');

        $this->entityManager = static::createClient()->getContainer()
            ->get('doctrine')
            ->getManager();

        parent::setUp();
    }

    private static function runCommand($command)
    {
        $command = sprintf('%s --quiet', $command);

        return self::getApplication()->run(new StringInput($command));
    }

    private static function getApplication()
    {
        if (null === self::$application) {
            $client = static::createClient();

            self::$application = new Application($client->getKernel());
            self::$application->setAutoExit(false);
        }

        return self::$application;
    }

    protected function tearDown()
    {
        self::runCommand('doctrine:database:drop --force');

        parent::tearDown();

        $this->entityManager->close();
        $this->entityManager = null; // avoid memory leaks
    }
}