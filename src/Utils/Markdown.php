<?php

declare(strict_types=1);

namespace Bolt\Utils;

use Parsedown;

class Markdown
{
    /** @var Parsedown */
    private $parser;

    public function __construct()
    {
        $this->parser = new Parsedown();
    }

    public function parse(string $text): string
    {
        return $this->parser->text($text);
    }
}
