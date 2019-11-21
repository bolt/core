<?php

declare(strict_types=1);

namespace Bolt\Controller\Backend\Async;

use Bolt\Configuration\Config;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Tightenco\Collect\Support\Collection;

/**
 * @Security("is_granted('ROLE_ADMIN')")
 */
class FileListingController implements AsyncZone
{
    /** @var Config */
    private $config;

    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    /**
     * @Route("/list_files", name="bolt_async_filelisting", methods={"GET"})
     */
    public function index(Request $request): JsonResponse
    {
        $locationName = $request->query->get('location', 'files');
        $type = $request->query->get('type', '');

        $path = $this->config->getPath($locationName, true);

        $files = $this->getFilesIndex($path, $type);

        return new JsonResponse($files);
    }

    private function getFilesIndex(string $path, string $type): Collection
    {
        if ($type === 'images') {
            $glob = '*.{jpg,png,gif,jpeg}';
        } else {
            $glob = null;
        }

        $files = [];

        foreach ($this->findFiles($path, $glob) as $file) {
            $files[] = [
                'group' => $file->getRelativePath(),
                'value' => $file->getRelativePathname(),
                'text' => $file->getFilename(),
            ];
        }

        return new Collection($files);
    }

    private function findFiles(string $path, ?string $glob = null): Finder
    {
        $finder = new Finder();
        $finder->in($path)->depth('< 3')->sortByType()->files();

        if ($glob) {
            $finder->name($glob);
        }

        return $finder;
    }
}
