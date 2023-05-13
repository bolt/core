<?php

declare(strict_types=1);

namespace Bolt\Controller\Backend;

use Bolt\Common\Str;
use Bolt\Configuration\FileLocation;
use Bolt\Configuration\FileLocations;
use Bolt\Controller\CsrfTrait;
use Bolt\Controller\TwigAwareController;
use Bolt\Repository\MediaRepository;
use Bolt\Utils\Excerpt;
use Bolt\Utils\FilesystemManager;
use League\Flysystem\DirectoryAttributes;
use League\Flysystem\DirectoryListing;
use League\Flysystem\FileAttributes;
use Pagerfanta\Adapter\ArrayAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Path;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;

class FilemanagerController extends TwigAwareController implements BackendZoneInterface
{
    use CsrfTrait;

    /** @var FileLocations */
    private $fileLocations;

    /** @var MediaRepository */
    private $mediaRepository;

    /** @var RequestStack */
    protected $requestStack;

    private const PAGESIZE = 60;

    /** @var FilesystemManager */
    private $filesystemManager;

    public function __construct(FileLocations $fileLocations, MediaRepository $mediaRepository, RequestStack $requestStack, FilesystemManager $filesystemManager)
    {
        $this->fileLocations = $fileLocations;
        $this->mediaRepository = $mediaRepository;
        $this->filesystemManager = $filesystemManager;
        $this->requestStack = $requestStack;
    }

    /**
     * @Route("/filemanager/{location}", name="bolt_filemanager", methods={"GET"})
     */
    public function filemanager(string $location): Response
    {
        $session = $this->requestStack->getSession();

        $this->denyAccessUnlessGranted('managefiles:' . $location);

        $path = $this->getFromRequest('path', '');
        if (str::endsWith($path, '/') === false) {
            $path .= '/';
        }
        if (str::startsWith($path, '/') === false) {
            $path = '/' . $path;
        }

        if ($this->getFromRequest('view')) {
            $view = $this->getFromRequest('view') === 'cards' ? 'cards' : 'list';
            $session->set('filemanager_view', $view);
        } else {
            $view = $this->getFromRequest('filemanager_view', 'list');
        }

        $location = $this->fileLocations->get($location);

        $finder = $this->findFiles($location, $path);
        $folders = $this->findFolders($location, $path);

        $currentPage = (int) $this->getFromRequest('page', '1');
        $pager = $this->createPaginator($finder, $currentPage);

        $parent = $path !== '/' ? Path::canonicalize($path . '/..') : '';

        return $this->render('@bolt/finder/finder.html.twig', [
            'path' => $path,
            'location' => $location,
            'finder' => $pager,
            'folders' => $folders,
            'parent' => $parent,
            'media' => $this->mediaRepository->findAll(),
            'allfiles' => $location->isShowAll() ? $this->buildIndex('/', $location) : false,
            'view' => $view,
        ]);
    }

    /**
     * @Route("/filemanager-actions/delete/", name="bolt_filemanager_delete", methods={"POST", "GET"})
     */
    public function delete(): Response
    {
        try {
            $this->validateCsrf('filemanager-delete');
        } catch (InvalidCsrfTokenException $e) {
            return new JsonResponse([
                'error' => [
                    'message' => 'Invalid CSRF token',
                ],
            ], Response::HTTP_FORBIDDEN);
        }

        $path = $this->getFromRequest('path');
        $location = $this->getFromRequest('location');

        $this->denyAccessUnlessGranted('managefiles:' . $location);

        $location = $this->fileLocations->get($location);
        $folder =  '/' . $path;
        $filesystem = $this->filesystemManager->get($location->getKey());

        if (! $filesystem->directoryExists($folder)) {
            $this->addFlash('warning', 'filemanager.delete_folder_missing');
        } else {
            try {
                $filesystem->deleteDirectory($folder);
                $this->addFlash('success', 'filemanager.delete_folder_successful');
            } catch (IOException $e) {
                $this->addFlash('danger', 'filemanager.delete_folder_error');
            }
        }

        return $this->redirectToRoute('bolt_filemanager', [
            'location' => $this->getFromRequest('location'),
            'path' => Path::canonicalize($path . '/..'),
        ]);
    }

    /**
     * @Route("/filemanager-actions/create", name="bolt_filemanager_create", methods={"POST"})
     */
    public function create(): Response
    {
        try {
            $this->validateCsrf('filemanager-create');
        } catch (InvalidCsrfTokenException $e) {
            return new JsonResponse([
                'error' => [
                    'message' => 'Invalid CSRF token',
                ],
            ], Response::HTTP_FORBIDDEN);
        }

        $path = $this->getFromRequest('path') . $this->getFromRequest('folderName');
        $location = $this->getFromRequest('location');

        $this->denyAccessUnlessGranted('managefiles:' . $location);

        $location = $this->fileLocations->get($location);
        $folder =  '/' . $path;
        $filesystem = $this->filesystemManager->get($location->getKey());

        if ($filesystem->directoryExists($folder)) {
            $this->addFlash('warning', 'filemanager.create_folder_already_exists');
            $this->addFlash('danger', 'filemanager.create_folder_error');
        } else {
            try {
                $filesystem->createDirectory($folder, ['visibility' => 'public']);
                $this->addFlash('success', 'filemanager.create_folder_success');
            } catch (IOException $exception) {
                $this->addFlash('danger', 'filemanager.create_folder_error');
            }
        }

        return $this->redirectToRoute('bolt_filemanager', [
            'location' => $this->getFromRequest('location'),
            'path' => Path::canonicalize($path . '/..'),
        ]);
    }

    private function findFiles(FileLocation $location, string $path): DirectoryListing
    {
        return $this->filesystemManager->get($location->getKey())
            ->listContents($path, false)
            ->filter(fn($item) => $item instanceof FileAttributes);
    }

    private function findFolders(FileLocation $location, string $path): DirectoryListing
    {
        return $this->filesystemManager->get($location->getKey())
            ->listContents($path, false)
            ->filter(fn($item) => $item instanceof DirectoryAttributes);
    }

    private function createPaginator(DirectoryListing $listing, int $page): Pagerfanta
    {
        $paginator = new Pagerfanta(new ArrayAdapter(iterator_to_array($listing->getIterator(), true)));
        $paginator->setMaxPerPage(self::PAGESIZE);
        $paginator->setCurrentPage($page);

        return $paginator;
    }

    private function buildIndex(string $base, FileLocation $location)
    {
        $filesystem = $this->filesystemManager->get($location->getKey());

        $files = $filesystem
            ->listContents($base, true)
            ->filter(fn($item) => $item instanceof FileAttributes);

        $index = [];

        foreach ($files as $file) {
            $contents = $this->getFileSummary(
                $filesystem->read($file->path())
            );
            $index[] = [
                'filename' => $file->path(),
                'description' => $contents,
            ];
        }

        return $index;
    }

    private function getFileSummary($contents)
    {
        $contents = str_replace(['<?php', '# ', "\n"], ['', '', " \n"], $contents);

        return Excerpt::getExcerpt($contents, 300);
    }
}
