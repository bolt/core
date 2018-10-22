<?php

declare(strict_types=1);

namespace Bolt\Controller\Async;

use Bolt\Configuration\Config;
use Bolt\Content\MediaFactory;
use Bolt\Media\RequestHandler;
use Cocur\Slugify\Slugify;
use Doctrine\Common\Persistence\ObjectManager;
use Sirius\Upload\Handler;
use Sirius\Upload\Result\File;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Webmozart\PathUtil\Path;

class Uploader
{
    /** @var MediaFactory */
    private $mediaFactory;

    /** @var RequestHandler */
    private $requestHandler;

    /** @var ObjectManager */
    private $manager;

    /** @var Config */
    private $config;

    public function __construct(MediaFactory $mediaFactory, RequestHandler $requestHandler, ObjectManager $manager, Config $config)
    {
        $this->mediaFactory = $mediaFactory;
        $this->requestHandler = $requestHandler;
        $this->manager = $manager;
        $this->config = $config;
    }

    /**
     * @Route("/async/upload", name="bolt_upload_post", methods={"POST"})
     */
    public function upload(Request $request)
    {
//        $uploadHandler = new Handler('/path/to/local_folder');

        $area = $request->query->get('area', '');
        $path = $request->query->get('path', '');

        $target = $this->config->getPath($area, true, $path);

        $uploadHandler = new Handler($target, [
            Handler::OPTION_AUTOCONFIRM => true,
            Handler::OPTION_OVERWRITE => true,
        ]);

        $uploadHandler->addRule('extension', ['allowed' => 'jpg', 'jpeg', 'png'], '{label} should be a valid image (jpg, jpeg, png)', 'Profile picture');
        $uploadHandler->addRule('size', ['max' => '20M'], '{label} should have less than {max}', 'Profile picture');
        $uploadHandler->setSanitizerCallback(function ($name) {
            return $this->sanitiseFilename($name);
        });

        /** @var File $result */
        $result = $uploadHandler->process($_FILES);

        if ($result->isValid()) {
            try {
                $media = $this->mediaFactory->createFromFilename($area, $path, $result->name);
                $this->manager->persist($media);
                $this->manager->flush();

                return new Response($result->name);
            } catch (\Exception $e) {
                // something wrong happened, we don't need the uploaded files anymore
                $result->clear();
                throw $e;
            }
        } else {
            // image was not moved to the container, where are error messages
            $messages = $result->getMessages();
        }

        return new Response('Not OK');
    }

    /**
     * @param string $filename
     *
     * @return string
     */
    private function sanitiseFilename(string $filename): string
    {
        $extensionSlug = new Slugify(['regexp' => '/([^a-z0-9]|-)+/']);
        $filenameSlug = new Slugify(['lowercase' => false]);

        $extension = $extensionSlug->slugify(Path::getExtension($filename));
        $filename = $filenameSlug->slugify(Path::getFilenameWithoutExtension($filename));

        return $filename . '.' . $extension;
    }
}
