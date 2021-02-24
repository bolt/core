<?php

declare(strict_types=1);

namespace Bolt\Controller\Backend\Async;

use Bolt\Configuration\Config;
use Bolt\Utils\PathCanonicalize;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Annotation\Route;
use Tightenco\Collect\Support\Collection;
use Webmozart\PathUtil\Path;

/**
 * @Security("is_granted('ROLE_ADMIN')")
 */
class FileListingController implements AsyncZoneInterface
{
    /** @var Config */
    private $config;

    /** @var Request */
    private $request;

    /** @var string */
    private $publicPath;

    public function __construct(Config $config, RequestStack $requestStack, string $projectDir, string $publicFolder)
    {
        $this->config = $config;
        $this->request = $requestStack->getCurrentRequest();
        $this->publicPath = $projectDir . DIRECTORY_SEPARATOR . $publicFolder;
    }

    /**
     * @Route("/list_files", name="bolt_async_filelisting", methods={"GET"})
     */
    public function index(): JsonResponse
    {
        $locationName = $this->request->query->get('location', 'files');
        $type = $this->request->query->get('type', '');

        // @todo: config->getPath does not return the correct relative URL.
        // Hence, we use the Path::makeRelative. Fix this once config generates the correct relative path.
        $relativeLocation = Path::makeRelative($this->config->getPath($locationName, false), $this->publicPath);

        // Do not allow any path outside of the public directory.
        $path = PathCanonicalize::canonicalize($this->publicPath, $relativeLocation);

        $files = $this->getFilesIndex($path, $type);

        return new JsonResponse($files);
    }

    private function getFilesIndex(string $path, string $type): Collection
    {
        if ($type === 'images') {
            $glob = sprintf('*.{%s}', $this->config->getMediaTypes()->implode(','));
        } else {
            $glob = null;
        }

        $files = [];

        foreach ($this->findFiles($path, $glob) as $file) {
            $files[] = [
                'group' => Path::canonicalize($file->getRelativePath()),
                'value' => Path::canonicalize($file->getRelativePathname()),
                'text' => $file->getFilename(),
            ];
        }

        return new Collection($files);
    }

    private function findFiles(string $path, ?string $glob = null): Finder
    {
        $finder = new Finder();
        $finder->in($path)->depth('< 5')->sortByType()->files();

        if ($glob) {
            $finder->name($glob);
        }

        return $finder;
    }
}
