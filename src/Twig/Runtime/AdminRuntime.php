<?php declare(strict_types=1);

namespace Bolt\Twig\Runtime;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Bolt specific Twig functions and filters for backend.
 */
class AdminRuntime
{
    /** @var UrlGeneratorInterface */
    private $urlGenerator;

    /**
     * Constructor.
     *
     * @param UrlGeneratorInterface $urlGenerator
     */
    public function __construct(
        UrlGeneratorInterface $urlGenerator
    ) {
        $this->urlGenerator = $urlGenerator;
    }

    public function dummy($input = null)
    {
        return $input;
    }

    /**
     * Create a link to edit a .yml file, if a filename is detected in the string. Mostly
     * for use in Flashbag messages, to allow easy editing.
     *
     * @param string $str
     *
     * @return string Resulting string
     */
    public function ymllink($str)
    {
        return preg_replace_callback(
            '/([a-z0-9_-]+\.yml)/i',
            function ($matches) {
                $path = $this->urlGenerator->generate('bolt_edit_file', ['area' => 'config', 'file' => '/' . $matches[1]]);
                $link = sprintf(' <a href="%s">%s</a>', $path, $matches[1]);

                return $link;
            },
            $str
        );
    }
}
