<?php

declare(strict_types=1);

namespace Bolt\Controller\Backend\Async;

use Bolt\Configuration\Config;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Tightenco\Collect\Support\Collection;

class FileListingController implements AsyncZoneInterface
{
    /** @var Config */
    private $config;

    /** @var Request */
    private $request;

    private $security;

    public function __construct(Config $config, RequestStack $requestStack, Security $security)
    {
        $this->config = $config;
        $this->request = $requestStack->getCurrentRequest();
        $this->security = $security;
    }

    /**
     * @Route("/list_files", name="bolt_async_filelisting", methods={"GET"})
     */
    public function index(): JsonResponse
    {
        $locationName = $this->request->query->get('location', 'files');
        $type = $this->request->query->get('type', '');

        if (!$this->security->isGranted('list_files:' . $locationName)) {
            return new JsonResponse("permission denied", Response::HTTP_UNAUTHORIZED);
        }

        $path = $this->config->getPath($locationName, true);

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
        $finder->in($path)->depth('< 5')->sortByType()->files();

        if ($glob) {
            $finder->name($glob);
        }

        return $finder;
    }
}
