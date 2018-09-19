<?php

declare(strict_types=1);

namespace Bolt\Controller;

use Bolt\Configuration\Config;
use League\Glide\Responses\SymfonyResponseFactory;
use League\Glide\ServerFactory;
use Symfony\Component\Routing\Annotation\Route;

class ImageController
{
    private $config;

    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    /**
     * @Route("/thumbs/{filename}", methods={"GET"}, name="thumbnail")
     */
    public function image(string $filename)
    {
        $server = ServerFactory::create([
            'response' => new SymfonyResponseFactory(),
            'source' => $this->config->path('files'),
            'cache' => $this->config->path('cache', true, 'thumbnails'),
        ]);

        $server->outputImage($filename, $_GET);
    }
}
