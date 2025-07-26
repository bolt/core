<?php

namespace Bolt\Twig;

use Symfony\Bridge\Twig\Extension\AssetExtension;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Filesystem\Filesystem;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class AssetsExtension extends AbstractExtension
{
    public function __construct(
        private readonly AssetExtension $assets,
        private readonly ContainerInterface $container,
        private readonly Filesystem $filesystem
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('asset', $this->getAssetUrl(...)),
        ];
    }

    /**
     * Returns the public url/path of an asset.
     *
     * If the package used to generate the path is an instance of
     * UrlPackage, you will always get a URL and not a path.
     */
    public function getAssetUrl(string $path, ?string $packageName = null): string
    {
        $url = $this->assets->getAssetUrl($path, $packageName);
        $original = $url;

        // Check if it exists in Symfony's default public dir.
        $absolutePath = $this->getAbsolutePath($url);
        if (! $this->filesystem->exists($absolutePath) && $packageName === null) {
            $url = $this->assets->getAssetUrl($path, 'public');
        }

        // If it doesn't exist in the public dir, return the originally generated url.
        $absolutePath = $this->getAbsolutePath($url);
        if (! $this->filesystem->exists($absolutePath)) {
            $url = $original;
        }

        return $url;
    }

    private function getAbsolutePath(string $url): string
    {
        return sprintf(
            '%s/%s%s',
            $this->container->getParameter('kernel.project_dir'),
            $this->container->getParameter('bolt.public_folder'),
            $url
        );
    }
}
