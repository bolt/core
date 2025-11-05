<?php

declare(strict_types=1);

namespace Bolt\Controller\Backend;

use Bolt\Common\Str;
use Bolt\Controller\CsrfTrait;
use Bolt\Controller\TwigAwareController;
use Bolt\Repository\MediaRepository;
use Bolt\Utils\PathCanonicalize;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Path;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Yaml;
use Throwable;
use Webimpress\SafeWriter\Exception\ExceptionInterface;
use Webimpress\SafeWriter\FileWriter;

class FileEditController extends TwigAwareController implements BackendZoneInterface
{
    use CsrfTrait;

    private Filesystem $filesystem;

    public function __construct(
        private MediaRepository $mediaRepository,
        private EntityManagerInterface $em
    ) {
        $this->filesystem = new Filesystem();
    }

    #[Route(path: '/file-edit/{location}', name: 'bolt_file_edit', methods: [Request::METHOD_GET])]
    public function edit(string $location): Response
    {
        $this->denyAccessUnlessGranted('managefiles:' . $location);

        $file = $this->getFromRequest('file');
        $basepath = $this->config->getPath($location);

        $filename = PathCanonicalize::canonicalize($basepath, $file);

        $contents = file_get_contents($filename);

        $context = [
            'location' => $location,
            'file' => $file,
            'contents' => $contents,
            'writable' => is_writable($filename),
        ];

        return $this->render('@bolt/finder/editfile.html.twig', $context);
    }

    #[Route(path: '/file-edit/{location}', name: 'bolt_file-edit_post', requirements: ['file' => '.+'], methods: [Request::METHOD_POST])]
    public function save(UrlGeneratorInterface $urlGenerator): Response
    {
        $this->validateCsrf('editfile');

        $file = $this->getFromRequest('file');
        $locationName = $this->getFromRequest('location');

        $this->denyAccessUnlessGranted('managefiles:' . $locationName);

        $contents = $this->getFromRequestRaw('editfile');
        $extension = Path::getExtension($file);

        $basepath = $this->config->getPath($locationName);
        $filename = $this->config->getPath($basepath, true, $file);

        // Make sure we don't rename the file to something that we're not allowed to, or move it out of the root
        if ((! $this->config->getFileTypes()->contains($extension)) ||
            (Str::startsWith(Path::makeRelative($filename, $basepath), '../'))) {
            $this->addFlash('warning', 'You are not allowed to do that.');
            return $this->redirectToRoute('bolt_dashboard');
        }

        $url = $urlGenerator->generate('bolt_file_edit', [
            'location' => $locationName,
            'file' => $file,
        ]);

        if (in_array($extension, ['yml', 'yaml'], true) && ! $this->verifyYaml($contents)) {
            $context = [
                'location' => $locationName,
                'file' => $file,
                'contents' => $contents,
                'writable' => is_writable($filename),
            ];

            return $this->render('@bolt/finder/editfile.html.twig', $context);
        }

        try {
            FileWriter::writeFile($filename, $contents);
            $this->addFlash('success', 'editfile.updated_successfully');
        } catch (ExceptionInterface) {
            $this->addFlash('warning', 'editfile.could_not_write');
        }

        return new RedirectResponse($url);
    }

    #[Route(path: '/file-delete/', name: 'bolt_file_delete', methods: [Request::METHOD_GET, Request::METHOD_POST])]
    public function handleDelete(): Response
    {
        try {
            $this->validateCsrf('file-delete');
        } catch (InvalidCsrfTokenException $e) {
            return new JsonResponse([
                'error' => [
                    'message' => 'Invalid CSRF token',
                ],
            ], Response::HTTP_FORBIDDEN);
        }

        $locationName = $this->getFromRequest('location', '');

        $this->denyAccessUnlessGranted('managefiles:' . $locationName);

        $path = $this->getFromRequest('path', '');

        $media = $this->mediaRepository->findOneByFullFilename($path, $locationName);

        if ($media !== null) {
            // If there is a media for the file, make sure to delete it.
            $this->em->remove($media);
            $this->em->flush();
        }

        $filePath = PathCanonicalize::canonicalize($locationName, $path);

        try {
            $this->filesystem->remove($filePath);
        } catch (Throwable $e) {
            // something wrong happened, we don't need the uploaded files anymore
            throw $e;
        }

        $this->addFlash('success', 'file.delete_success');

        $folder = pathinfo((string) $path, PATHINFO_DIRNAME);

        return $this->redirectToRoute('bolt_filemanager', [
            'location' => $locationName,
            'path' => $folder,
        ]);
    }

    #[Route(path: '/file-duplicate/', name: 'bolt_file_duplicate', methods: [Request::METHOD_GET, Request::METHOD_POST])]
    public function handleDuplicate(): Response
    {
        try {
            $this->validateCsrf('file-duplicate');
        } catch (InvalidCsrfTokenException $e) {
            return new JsonResponse([
                'error' => [
                    'message' => 'Invalid CSRF token',
                ],
            ], Response::HTTP_FORBIDDEN);
        }

        $locationName = $this->getFromRequest('location', '');

        $this->denyAccessUnlessGranted('managefiles:' . $locationName);

        $path = $this->getFromRequest('path', '');

        $originalFilepath = PathCanonicalize::canonicalize($locationName, $path);

        $copyFilePath = $this->getCopyFilepath($originalFilepath);

        try {
            $this->filesystem->copy($originalFilepath, $copyFilePath);
        } catch (Throwable $e) {
            // something wrong happened, we don't need the uploaded files anymore
            throw $e;
        }

        $this->addFlash('success', 'file.duplicate_success');
        $folder = pathinfo((string) $path, PATHINFO_DIRNAME);

        return $this->redirectToRoute('bolt_filemanager', [
            'location' => $locationName,
            'path' => $folder,
        ]);
    }

    /**
     * @return string Returns the copy file path. E.g. 'files/foal.jpg' -> 'files/foal (1).jpg'
     */
    private function getCopyFilepath(string $path): string
    {
        $copyPath = $path;

        $i = 1;
        while ($this->filesystem->exists($copyPath)) {
            $pathinfo = pathinfo($path);
            $basename = basename($pathinfo['basename'], '.' . $pathinfo['extension']) . ' (' . $i . ')' . '.' . $pathinfo['extension'];
            $copyPath = PathCanonicalize::canonicalize($pathinfo['dirname'], $basename);
            $i++;
        }

        return $copyPath;
    }

    private function verifyYaml(string $yaml): bool
    {
        try {
            Yaml::parse($yaml, Yaml::PARSE_CUSTOM_TAGS);
        } catch (ParseException $e) {
            $this->addFlash('error', $e->getMessage());

            return false;
        }

        return true;
    }
}
