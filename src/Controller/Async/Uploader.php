<?php

declare(strict_types=1);

namespace Bolt\Controller\Async;

use Bolt\Content\MediaFactory;
use Bolt\Media\RequestHandler;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class Uploader
{
    /** @var MediaFactory */
    private $mediaFactory;

    /** @var RequestHandler */
    private $requestHandler;

    /** @var ObjectManager */
    private $manager;

    public function __construct(MediaFactory $mediaFactory, RequestHandler $requestHandler, ObjectManager $manager)
    {
        $this->mediaFactory = $mediaFactory;
        $this->requestHandler = $requestHandler;
        $this->manager = $manager;
    }

    /**
     * @Route("/async/upload", name="bolt_upload_post", methods={"POST"})
     */
    public function upload(Request $request)
    {
        // Get submitted field data item, will always be one item in case of async upload
        $items = $this->requestHandler->loadFilesByField('filepond');

        // If no items, exit
        if (count($items) === 0) {
            // Something went wrong, most likely a field name mismatch
            http_response_code(400);

            return;
        }

        $params = $request->request->get('filepond', []);

        foreach ($items as $item) {
            $media = $this->mediaFactory->createFromUpload($item, current($params));
            $this->manager->persist($media);
            $this->manager->flush();
        }

        return new Response($media->getPath() . $media->getFilename());
    }
}
