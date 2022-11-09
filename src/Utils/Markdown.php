<?php

declare(strict_types=1);

namespace Bolt\Utils;

use ParsedownExtra;

class Markdown
{
    /** @var ParsedownExtra */
    private $parser;

    public function __construct()
    {
        $this->parser = new ParsedownExtra();
    }

    public function parse(string $text): string
    {
        return $this->parser->text($text);
    }
}
