<?php

declare(strict_types=1);

namespace Bolt\Controller\Backend\Async;

use Bolt\Common\Str;
use Bolt\Configuration\Config;
use Bolt\Controller\CsrfTrait;
use Bolt\Factory\MediaFactory;
use Bolt\Twig\TextExtension;
use Cocur\Slugify\Slugify;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sirius\Upload\Handler;
use Sirius\Upload\Result\Collection;
use Sirius\Upload\Result\ResultInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Path;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\FileBag;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;
use Symfony\Component\Validator\Constraints\Url;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;
use Throwable;

/**
 * @Security("is_granted('upload')")
 */
class UploadController extends AbstractController implements AsyncZoneInterface
{
    use CsrfTrait;

    /** @var MediaFactory */
    private $mediaFactory;

    /** @var EntityManagerInterface */
    private $em;

    /** @var Config */
    private $config;

    /** @var TextExtension */
    private $textExtension;

    /** @var Request */
    private $request;

    /** @var Filesystem */
    private $filesystem;

    /** @var TagAwareCacheInterface */
    private $cache;

    public function __construct(MediaFactory           $mediaFactory,
                                EntityManagerInterface $em,
                                Config                 $config,
                                TextExtension          $textExtension,
                                RequestStack           $requestStack,
                                Filesystem             $filesystem,
                                TagAwareCacheInterface $cache)
    {
        $this->mediaFactory = $mediaFactory;
        $this->em = $em;
        $this->config = $config;
        $this->textExtension = $textExtension;
        $this->request = $requestStack->getCurrentRequest();
        $this->filesystem = $filesystem;
        $this->cache = $cache;
    }

    /**
     * @Route("/upload-url", name="bolt_async_upload_url", methods={"POST"})
     */
    public function handleURLUpload(Request $request, ValidatorInterface $validator): Response
    {
        try {
            $this->validateCsrf('upload');
        } catch (InvalidCsrfTokenException $e) {
            return new JsonResponse([
                'error' => [
                    'message' => 'Invalid CSRF token',
                ],
            ], Response::HTTP_FORBIDDEN);
        }

        $url = $request->get('url', '');

        // Make sure the submitting URL is a valid URL
        $violations = $validator->validate($url, new Url());
        if ($violations->count() !== 0) {
            return new JsonResponse([
                'error' => [
                    'message' => $violations->get(0)->getMessage(),
                ],
            ], Response::HTTP_BAD_REQUEST);
        }

        $tmpFolder = $this->getParameter('kernel.cache_dir') . DIRECTORY_SEPARATOR . 'tmpupload';
        $tmpFile = $tmpFolder . DIRECTORY_SEPARATOR . bin2hex(random_bytes(6));

        try {
            // Make sure temporary folder exists
            $this->filesystem->mkdir($tmpFolder);
            // Create temporary file
            $this->filesystem->copy($url, $tmpFile);
        } catch (Throwable $e) {
            return new JsonResponse([
                'error' => [
                    'message' => $e->getMessage(),
                ],
            ], Response::HTTP_BAD_REQUEST);
        }

        $file = new UploadedFile($tmpFile, basename($url));
        $bag = new FileBag();
        $bag->add([$file]);
        $request->files = $bag;

        $response = $this->handleUpload($request);

        // The file is automatically deleted. It may be that we don't need this.
        $this->filesystem->remove($tmpFile);

        return $response;
    }

    /**
     * @Route("/upload", name="bolt_async_upload", methods={"POST"})
     */
    public function handleUpload(Request $request): JsonResponse
    {
        try {
            $this->validateCsrf('upload');
        } catch (InvalidCsrfTokenException $e) {
            return new JsonResponse([
                'error' => [
                    'message' => 'Invalid CSRF token',
                ],
            ], Response::HTTP_FORBIDDEN);
        }

        $locationName = $this->request->query->get('location', '');
        $path = $this->request->query->get('path', '');

        $basepath = $this->config->getPath($locationName);
        $target = $this->config->getPath($locationName, true, $path);

        // Make sure we don't move it out of the root.
        if (Str::startsWith(path::makeRelative($target, $basepath), '../')) {
            return new JsonResponse([
                'error' => [
                    'message' => "You are not allowed to do that.",
                ],
            ], Response::HTTP_BAD_REQUEST);
        }

        $uploadHandler = new Handler($target, [
            Handler::OPTION_AUTOCONFIRM => true,
            Handler::OPTION_OVERWRITE => false,
        ]);

        $acceptedFileTypes = array_merge($this->config->getMediaTypes()->toArray(), $this->config->getFileTypes()->toArray());
        $maxSize = $this->config->getMaxUpload();

        $uploadHandler->addRule(
            'extension',
            ['allowed' => $acceptedFileTypes],
            'The file for field \'{label}\' was <u>not</u> uploaded. It should be a valid file type. Allowed are <code>' . implode('</code>, <code>', $acceptedFileTypes) . '.',
            'Upload file'
        );

        $uploadHandler->addRule(
            'size',
            ['size' => $maxSize],
            'The file for field \'{label}\' was <u>not</u> uploaded. The upload can have a maximum filesize of <b>' . $this->textExtension->formatBytes($maxSize) . '</b>.',
            'Upload file'
        );

        $uploadHandler->addRule(
            'callback',
            ['callback' => [$this, 'checkJavascriptInSVG']],
            'It is not allowed to upload SVG\'s with embedded Javascript.',
            'Upload file'
        );

        $uploadHandler->setSanitizerCallback(function ($name) {
            return $this->sanitiseFilename($name);
        });

        // Clear the 'files_index' cache.
        $this->cache->invalidateTags(['fileslisting']);

        try {
            /** @var UploadedFile|File|ResultInterface|Collection $result */
            $result = $uploadHandler->process($request->files->all());
        } catch (\Throwable $e) {
            return new JsonResponse([
                'error' => [
                    'message' => $e->getMessage() . ' Ensure the upload does <em><u>not</u></em> exceed the maximum filesize of <b>' . $this->textExtension->formatBytes($maxSize) . '</b>, and that the destination folder (on the webserver) is writable.',
                ],
            ], Response::HTTP_BAD_REQUEST);
        }

        if ($result->isValid()) {
            try {
                $media = $this->mediaFactory->createFromFilename($locationName, $path, $result->__get('name'));

                if ($this->mediaFactory->isImage($media)) {
                    $this->em->persist($media);
                    $this->em->flush();
                }

                return new JsonResponse($media->getFilenamePath());
            } catch (\Throwable $e) {
                // something wrong happened, we don't need the uploaded files anymore
                $result->clear();

                throw $e;
            }
        }

        // image was not moved to the container, where are error messages
        $messages = $result->getMessages();

        return new JsonResponse([
            'error' => [
                'message' => implode(', ', $messages),
            ],
        ], Response::HTTP_BAD_REQUEST);
    }

    private function sanitiseFilename(string $filename): string
    {
        $extensionSlug = new Slugify(['regexp' => '/([^a-z0-9]|-)+/']);
        $filenameSlug = new Slugify(['lowercase' => false]);

        $extension = $extensionSlug->slugify(Path::getExtension($filename));
        $filename = $filenameSlug->slugify(Path::getFilenameWithoutExtension($filename));

        return $filename . '.' . $extension;
    }

    public function checkJavascriptInSVG($file)
    {
        if (Path::getExtension($file['name']) != 'svg') {
            return true;
        }

        $svgFile = file_get_contents($file['tmp_name']);

        if (preg_match('/(?:<[^>]+\s)(on\S+)=["\']?((?:.(?!["\']?\s+(?:\S+)=|[>"\']))+.)["\']?/i', $svgFile)) {
            return false;
        }

        return (mb_strpos(preg_replace('/\s+/', '', mb_strtolower($svgFile)), '<script') === false);
    }
}

