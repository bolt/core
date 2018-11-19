<?php

declare(strict_types=1);

namespace Bolt\Twig;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class AdminExtension extends AbstractExtension
{
    /** @var UrlGeneratorInterface */
    private $urlGenerator;

    /**
     * Constructor.
     */
    public function __construct(UrlGeneratorInterface $urlGenerator)
    {
        $this->urlGenerator = $urlGenerator;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        $safe = ['is_safe' => ['html']];

        return [
            new TwigFilter('ymllink', [$this, 'ymllink'], $safe),
        ];
    }

    /**
     * Create a link to edit a .yml file, if a filename is detected in the string. Mostly
     * for use in Flashbag messages, to allow easy editing.
     *
     * @param string $str
     *
     * @return string Resulting string
     */
    public function ymllink($str): string
    {
        return preg_replace_callback(
            '/([a-z0-9_-]+\.yml)/i',
            function ($matches) {
                $path = $this->urlGenerator->generate('bolt_file_edit', [
                    'area' => 'config',
                    'file' => '/' . $matches[1],
                ]);
                return sprintf(' <a href="%s">%s</a>', $path, $matches[1]);
            },
            $str
        );
    }
}
