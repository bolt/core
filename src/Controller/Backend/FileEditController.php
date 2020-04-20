<?php

declare(strict_types=1);

namespace Bolt\Controller\Backend;

use Bolt\Controller\CsrfTrait;
use Bolt\Controller\TwigAwareController;
use Bolt\Repository\MediaRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Parser;
use Webimpress\SafeWriter\Exception\ExceptionInterface;
use Webimpress\SafeWriter\FileWriter;
use Webmozart\PathUtil\Path;

/**
 * @Security("is_granted('ROLE_ADMIN')")
 */
class FileEditController extends TwigAwareController implements BackendZoneInterface
{
    use CsrfTrait;

    /** @var MediaRepository */
    private $mediaRepository;

    /** @var EntityManagerInterface */
    private $em;

    /** @var Filesystem */
    private $filesystem;

    public function __construct(CsrfTokenManagerInterface $csrfTokenManager, MediaRepository $mediaRepository, EntityManagerInterface $em)
    {
        $this->csrfTokenManager = $csrfTokenManager;
        $this->mediaRepository = $mediaRepository;
        $this->em = $em;
        $this->filesystem = new Filesystem();
    }

    /**
     * @Route("/file-edit/{location}", name="bolt_file_edit", methods={"GET"})
     */
    public function edit(string $location, Request $request): Response
    {
        $file = $request->query->get('file');
        if (mb_strpos($file, '/') !== 0) {
            $file = '/' . $file;
        }

        $basepath = $this->config->getPath($location);
        $filename = Path::canonicalize($basepath . '/' . $file);
        $contents = file_get_contents($filename);

        $context = [
            'location' => $location,
            'file' => $file,
            'contents' => $contents,
            'writable' => is_writable($filename),
        ];

        return $this->renderTemplate('@bolt/finder/editfile.html.twig', $context);
    }

    /**
     * @Route("/file-edit/{location}", name="bolt_file-edit_post", methods={"POST"}, requirements={"file"=".+"})
     */
    public function save(Request $request, UrlGeneratorInterface $urlGenerator): Response
    {
        $this->validateCsrf($request, 'editfile');

        $file = $request->request->get('file');
        $locationName = $request->request->get('location');
        $contents = $request->request->get('editfile');
        $extension = Path::getExtension($file);

        $url = $urlGenerator->generate('bolt_file_edit', [
            'location' => $locationName,
            'file' => $file,
        ]);

        if (in_array($extension, ['yml', 'yaml'], true) && ! $this->verifyYaml($contents)) {
            $context = [
                'location' => $locationName,
                'file' => $file,
                'contents' => $contents,
            ];

            return $this->renderTemplate('@bolt/finder/editfile.html.twig', $context);
        }

        $basepath = $this->config->getPath($locationName);
        $filename = Path::canonicalize($basepath . '/' . $file);

        try {
            FileWriter::writeFile($filename, $contents);
            $this->addFlash('success', 'editfile.updated_successfully');
        } catch (ExceptionInterface $e) {
            $this->addFlash('warning', 'editfile.could_not_write');
        }

        return new RedirectResponse($url);
    }

    /**
     * @Route("/file-delete/", name="bolt_file_delete", methods={"POST", "GET"})
     */
    public function handleDelete(Request $request): Response
    {
        try {
            $this->validateCsrf($request, 'file-delete');
        } catch (InvalidCsrfTokenException $e) {
            return new JsonResponse([
                'error' => [
                    'message' => 'Invalid CSRF token',
                ],
            ], Response::HTTP_FORBIDDEN);
        }

        $locationName = $request->get('location', '');
        $path = $request->get('path', '');

        $media = $this->mediaRepository->findOneByFullFilename($path, $locationName);

        if ($media !== null) {
            // If there is a media for the file, make sure to delete it.
            $this->em->remove($media);
            $this->em->flush();
        }

        $filePath = Path::canonicalize($locationName . '/' . $path);

        try {
            $this->filesystem->remove($filePath);
        } catch (\Throwable $e) {
            // something wrong happened, we don't need the uploaded files anymore
            throw $e;
        }

        $this->addFlash('success', 'file.delete_success');
        return $this->redirectToRoute('bolt_filemanager', ['location' => $locationName]);
    }

    /**
     * @Route("/file-duplicate/", name="bolt_file_duplicate", methods={"POST", "GET"})
     */
    public function handleDuplicate(Request $request): Response
    {
        try {
            $this->validateCsrf($request, 'file-duplicate');
        } catch (InvalidCsrfTokenException $e) {
            return new JsonResponse([
                'error' => [
                    'message' => 'Invalid CSRF token',
                ],
            ], Response::HTTP_FORBIDDEN);
        }

        $locationName = $request->get('location', '');
        $path = $request->get('path', '');

        $originalFilepath = Path::canonicalize($locationName . '/' . $path);

        $copyFilePath = $this->getCopyFilepath($originalFilepath);

        try {
            $this->filesystem->copy($originalFilepath, $copyFilePath);
        } catch (\Throwable $e) {
            // something wrong happened, we don't need the uploaded files anymore
            throw $e;
        }

        $this->addFlash('success', 'file.delete_success');
        return $this->redirectToRoute('bolt_filemanager', ['location' => $locationName]);
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
            $basename = basename($pathinfo['basename'], '.' . $pathinfo['extension']) . ' (' . $i . ')';
            $copyPath = Path::canonicalize($pathinfo['dirname'] . '/' . $basename . '.' . $pathinfo['extension']);
            $i++;
        }

        return $copyPath;
    }

    private function verifyYaml(string $yaml): bool
    {
        $yamlParser = new Parser();
        try {
            $yamlParser->parse($yaml);
        } catch (ParseException $e) {
            $this->addFlash('error', $e->getMessage());

            return false;
        }

        return true;
    }
}
