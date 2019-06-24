<?php

declare(strict_types=1);

namespace Bolt\Utils;

class Sanitiser
{
    private $purifier;

    public function __construct()
    {
        $purifierConfig = \HTMLPurifier_Config::create([
            'Cache.DefinitionImpl' => null, // Disable caching
        ]);
        $this->purifier = new \HTMLPurifier($purifierConfig);
    }

    public function clean(string $html): string
    {
        return $this->purifier->purify($html);
    }
}
