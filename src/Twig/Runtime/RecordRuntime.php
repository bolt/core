<?php declare(strict_types=1);

namespace Bolt\Twig\Runtime;

use Bolt\Helpers\Excerpt;

/**
 * Bolt specific Twig functions and filters that provide \Bolt\Legacy\Content manipulation.
 */
class RecordRuntime
{
    public function dummy($input = null)
    {
        return $input;
    }

    public function excerpt($text, $length = 100)
    {
        $excerpter = new Excerpt($text);

        return $excerpter->getExcerpt((int) $length);
    }
}
