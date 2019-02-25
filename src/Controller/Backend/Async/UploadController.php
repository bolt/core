<?php

declare(strict_types=1);

namespace Bolt\Controller\Backend\Async;

use Bolt\Configuration\Config;
use Bolt\Content\MediaFactory;
use Bolt\Controller\CsrfTrait;
use Cocur\Slugify\Slugify;
use Doctrine\Common\Persistence\ObjectManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sirius\Upload\Handler;
use Sirius\Upload\Result\File;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Webmozart\PathUtil\Path;

/**
 * @Security("has_role('ROLE_ADMIN')")
 */
class UploadController
{
    use CsrfTrait;

    /** @var MediaFactory */
    private $mediaFactory;

    /** @var ObjectManager */
    private $em;

    /** @var Config */
    private $config;

    public function __construct(MediaFactory $mediaFactory, ObjectManager $em, Config $config, CsrfTokenManagerInterface $csrfTokenManager)
    {
        $this->mediaFactory = $mediaFactory;
        $this->em = $em;
        $this->config = $config;
        $this->csrfTokenManager = $csrfTokenManager;
    }

    /**
     * @Route("/upload", name="bolt_async_upload", methods={"POST"})
     */
    public function upload(Request $request)
    {
        $this->validateCsrf($request, 'upload');

        $area = $request->query->get('area', '');
        $path = $request->query->get('path', '');

        $target = $this->config->getPath($area, true, $path);

        $uploadHandler = new Handler($target, [
            Handler::OPTION_AUTOCONFIRM => true,
            Handler::OPTION_OVERWRITE => true,
        ]);

        $uploadHandler->addRule(
            'extension',
            ['allowed' => 'jpg', 'jpeg', 'png'],
            '{label} should be a valid image (jpg, jpeg, png)',
            'Profile picture'
        );
        $uploadHandler->addRule(
            'size',
            ['max' => '20M'],
            '{label} should have less than {max}',
            'Profile picture'
        );
        $uploadHandler->setSanitizerCallback(function ($name) {
            return $this->sanitiseFilename($name);
        });

        /** @var File $result */
        $result = $uploadHandler->process($request->files->all());

        if ($result->isValid()) {
            try {
                $media = $this->mediaFactory->createFromFilename($area, $path, $result->__get('name'));
                $this->em->persist($media);
                $this->em->flush();

                return new Response($media->getFilenamePath());
            } catch (\Throwable $e) {
                // something wrong happened, we don't need the uploaded files anymore
                $result->clear();
                throw $e;
            }
        }

        // image was not moved to the container, where are error messages
        $messages = $result->getMessages();

        return new Response('Not OK: ' . implode(', ', $messages), 400);
    }

    private function sanitiseFilename(string $filename): string
    {
        $extensionSlug = new Slugify(['regexp' => '/([^a-z0-9]|-)+/']);
        $filenameSlug = new Slugify(['lowercase' => false]);

        $extension = $extensionSlug->slugify(Path::getExtension($filename));
        $filename = $filenameSlug->slugify(Path::getFilenameWithoutExtension($filename));

        return $filename . '.' . $extension;
    }
}
