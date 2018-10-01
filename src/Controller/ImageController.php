<?php

declare(strict_types=1);

namespace Bolt\Controller;

use Bolt\Configuration\Config;
use League\Glide\Responses\SymfonyResponseFactory;
use League\Glide\ServerFactory;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Routing\Annotation\Route;

class ImageController
{
    /** @var Config */
    private $config;

    /**
     * ImageController constructor.
     *
     * @param Config $config
     */
    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    /**
     * @Route("/thumbs/{filename}", methods={"GET"}, name="thumbnail")
     *
     * @param string $filename
     *
     * @return StreamedResponse
     */
    public function image(string $filename): StreamedResponse
    {
        $server = ServerFactory::create([
            'response' => new SymfonyResponseFactory(),
            'source' => $this->config->getPath('files'),
            'cache' => $this->config->getPath('cache', true, 'thumbnails'),
        ]);

        /** @var StreamedResponse $response */
        $response = $server->getImageResponse($filename, $_GET);

        return $response;
    }
}
