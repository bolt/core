<?php

declare(strict_types=1);

namespace Bolt\Controller\Backend;

use Bolt\Controller\CsrfTrait;
use Bolt\Controller\TwigAwareController;
use Bolt\Repository\MediaRepository;
use Bolt\Utils\FilesystemManager;
use Bolt\Utils\PathCanonicalize;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Filesystem\Path;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Yaml;

class FileEditController extends TwigAwareController implements BackendZoneInterface
{
    use CsrfTrait;

    /** @var MediaRepository */
    private $mediaRepository;

    /** @var EntityManagerInterface */
    private $em;

    /** @var FilesystemManager */
    private $filesystemManager;

    public function __construct(MediaRepository $mediaRepository, EntityManagerInterface $em, FilesystemManager $filesystemManager)
    {
        $this->mediaRepository = $mediaRepository;
        $this->em = $em;
        $this->filesystemManager = $filesystemManager;
    }

    /**
     * @Route("/file-edit/{location}", name="bolt_file_edit", methods={"GET"})
     */
    public function edit(string $location): Response
    {
        $this->denyAccessUnlessGranted('managefiles:' . $location);

        $file = $this->getFromRequest('file');

        $filesystem = $this->filesystemManager->get($location);
        $contents = $filesystem->read($file);

        $context = [
            'location' => $location,
            'file' => $file,
            'contents' => $contents,
            'writable' => $filesystem->visibility($file) === 'public',
        ];

        return $this->render('@bolt/finder/editfile.html.twig', $context);
    }

    /**
     * @Route("/file-edit/{location}", name="bolt_file-edit_post", methods={"POST"}, requirements={"file"=".+"})
     */
    public function save(UrlGeneratorInterface $urlGenerator): Response
    {
        $this->validateCsrf('editfile');

        $file = $this->getFromRequest('file');
        $locationName = $this->getFromRequest('location');

        $this->denyAccessUnlessGranted('managefiles:' . $locationName);

        $contents = $this->getFromRequestRaw('editfile');
        $extension = Path::getExtension($file);

        // Make sure we don't rename the file to something that we're not allowed to, or move it out of the root
        if ((! $this->config->getFileTypes()->contains($extension))) {
            $this->addFlash('warning', "You are not allowed to do that.");
            return $this->redirectToRoute('bolt_dashboard');
        }

        $url = $urlGenerator->generate('bolt_file_edit', [
            'location' => $locationName,
            'file' => $file,
        ]);

        $filesystem = $this->filesystemManager->get($locationName);

        if (in_array($extension, ['yml', 'yaml'], true) && ! $this->verifyYaml($contents)) {
            $context = [
                'location' => $locationName,
                'file' => $file,
                'contents' => $contents,
                'writable' => $filesystem->visibility($file) === 'public',
            ];

            return $this->render('@bolt/finder/editfile.html.twig', $context);
        }

        try {
            $filesystem->write($file, $contents);
            $this->addFlash('success', 'editfile.updated_successfully');
        } catch (\Throwable $e) {
            $this->addFlash('warning', 'editfile.could_not_write');
        }

        return new RedirectResponse($url);
    }

    /**
     * @Route("/file-delete/", name="bolt_file_delete", methods={"POST", "GET"})
     */
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

        try {
            $this->filesystemManager->get($locationName)->delete($path);
        } catch (\Throwable $e) {
            // something wrong happened, we don't need the uploaded files anymore
            throw $e;
        }

        $this->addFlash('success', 'file.delete_success');

        $folder = pathinfo($path, PATHINFO_DIRNAME);

        return $this->redirectToRoute('bolt_filemanager', [
            'location' => $locationName,
            'path' => $folder,
        ]);
    }

    /**
     * @Route("/file-duplicate/", name="bolt_file_duplicate", methods={"POST", "GET"})
     */
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

        $originalFilepath = PathCanonicalize::canonicalize('/', $path);

        $copyFilePath = $this->getCopyFilepath($locationName, $originalFilepath);

        try {
            $this->filesystemManager->get($locationName)->copy($originalFilepath, $copyFilePath);
        } catch (\Throwable $e) {
            // something wrong happened, we don't need the uploaded files anymore
            throw $e;
        }

        $this->addFlash('success', 'file.duplicate_success');
        $folder = pathinfo($path, PATHINFO_DIRNAME);

        return $this->redirectToRoute('bolt_filemanager', [
            'location' => $locationName,
            'path' => $folder,
        ]);
    }

    /**
     * @return string Returns the copy file path. E.g. 'files/foal.jpg' -> 'files/foal (1).jpg'
     */
    private function getCopyFilepath(string $location, string $path): string
    {
        $copyPath = $path;

        $i = 1;
        while ($this->filesystemManager->get($location)->fileExists($copyPath)) {
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
