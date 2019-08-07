<?php

declare(strict_types=1);

namespace Bolt;

use Bolt\Extension\BaseExtension;

class Bar extends BaseExtension
{
    public function getName(): string
    {
        return 'BarFoo';
    }

    public function getClass(): string
    {
        return static::class;
    }

    public function initialize(): void
    {
//        dump('JOEHOE');
    }
}
