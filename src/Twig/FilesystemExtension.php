<?php
declare(strict_types=1);

namespace Bolt\Twig;

use Bolt\Configuration\FileLocation;
use Bolt\Utils\FilesystemManager;
use League\Flysystem\FileAttributes;
use League\Flysystem\UnableToGeneratePublicUrl;
use Symfony\Component\Filesystem\Path;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class FilesystemExtension extends AbstractExtension
{
    /** @var FilesystemManager */
    private $filesystemManager;

    public function __construct(FilesystemManager $filesystemManager)
    {
        $this->filesystemManager = $filesystemManager;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('filesystem_url', [$this, 'filesystemUrl']),
            new TwigFunction('file_extension', [$this, 'fileExtension'])
        ];
    }

    public function filesystemUrl(FileAttributes $file, FileLocation $location): string
    {
        try {
            return $this->filesystemManager->get($location->getKey())
                ->publicUrl($file->path());
        } catch (UnableToGeneratePublicUrl $e) {
            return $location->getBasepath() . '/' . $file->path();
        }
    }

    public function fileExtension(FileAttributes $file): string
    {
        return Path::getExtension($file->path(), true);
    }
}
