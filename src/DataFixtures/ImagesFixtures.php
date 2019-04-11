<?php

declare(strict_types=1);

namespace Bolt\DataFixtures;

use Bolt\Collection\DeepCollection;
use Bolt\Configuration\Config;
use Bolt\Entity\Content;
use Bolt\Entity\Field;
use Bolt\Enum\Statuses;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Generator;
use Gedmo\Translatable\Entity\Repository\TranslationRepository;
use Gedmo\Translatable\Entity\Translation;
use Tightenco\Collect\Support\Collection;

class ImagesFixtures extends BaseFixture
{
    /** @var Generator */
    private $faker;

    /** @var Collection */
    private $config;

    private $lastTitle = null;

    /** @var array */
    private $presetRecords = [];

    const AMOUNT = 10;

    public function __construct(Config $config)
    {
        $this->urls = [
            'https://source.unsplash.com/1280x768/?business,workspace,interior/',
            'https://source.unsplash.com/1920x640/?cityscape,landscape,nature/',
            'https://source.unsplash.com/1280x768/?animal,koala,kitten,puppy,cute/',
            'https://source.unsplash.com/1280x768/?technology/',
        ];

    }



    public function load(ObjectManager $manager): void
    {
        $this->loadContent($manager);

        $manager->flush();
    }

    private function loadContent(ObjectManager $manager): void
    {
        echo "tralalala";

        $outputPath = dirname(dirname(__DIR__)) . '/public/files/stock/';

        if (!is_dir($outputPath)) {
            mkdir($outputPath);
        }

        for ($i = 1; $i < $this::AMOUNT; $i++) {
            $url = $this->urls[array_rand($this->urls)] . rand(10000, 99999);


        }


    }

}
