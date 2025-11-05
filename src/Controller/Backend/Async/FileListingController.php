<?php

declare(strict_types=1);

namespace Bolt\Controller\Backend\Async;

use Bolt\Configuration\Config;
use Bolt\Utils\FilesIndex;
use Bolt\Utils\PathCanonicalize;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Filesystem\Path;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class FileListingController implements AsyncZoneInterface
{
    private readonly ?Request $request;
    private readonly string $publicPath;

    public function __construct(
        private readonly Config $config,
        RequestStack $requestStack,
        private readonly Security $security,
        string $projectDir,
        string $publicFolder,
        private readonly FilesIndex $filesIndex
    ) {
        $this->request = $requestStack->getCurrentRequest();
        $this->publicPath = $projectDir . DIRECTORY_SEPARATOR . $publicFolder;
    }

    #[Route(path: '/list_files', name: 'bolt_async_filelisting', methods: [Request::METHOD_GET])]
    public function index(): JsonResponse
    {
        $locationName = $this->request?->query->getString('location', 'files') ?? 'files';
        $type = $this->request?->query->getString('type') ?? '';
        $locationTopLevel = explode('/', Path::canonicalize($locationName))[0];

        if (! $this->security->isGranted('list_files:' . $locationTopLevel)) {
            return new JsonResponse('permission denied', Response::HTTP_UNAUTHORIZED);
        }

        // @todo: config->getPath does not return the correct relative URL.
        // Hence, we use the Path::makeRelative. Fix this once config generates the correct relative path.
        $relativeLocation = Path::makeRelative($this->config->getPath($locationName, false), $this->publicPath);
        $relativeTopLocation = Path::makeRelative($this->config->getPath($locationTopLevel, false), $this->publicPath);

        // Do not allow any path outside of the public directory.
        $path = PathCanonicalize::canonicalize($this->publicPath, $relativeLocation);
        $baseFilePath = PathCanonicalize::canonicalize($this->publicPath, $relativeTopLocation);
        $baseUrlPath = $this->request?->getPathInfo() ?? '';
        $relativePath = Path::makeRelative($path, $this->publicPath);

        $files = $this->filesIndex->get($relativePath, $type, $baseUrlPath, $baseFilePath);

        return new JsonResponse($files);
    }
}
