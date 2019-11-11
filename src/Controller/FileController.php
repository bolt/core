<?php

declare(strict_types=1);

namespace Bolt\Controller;

use Bolt\Configuration\Config;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class FileController
{
    /** @var Config */
    private $config;

    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    /**
     * @Route("/files/{filename}", methods={"GET"}, name="thumbnail", requirements={"filename"=".+"})
     */
    public function file(string $filename, Request $request): Response
    {
        $location = $request->query->get('location', 'files');
        $filepath = sprintf('%s%s%s', $this->config->getPath($location), DIRECTORY_SEPARATOR, $filename);

        return new Response(file_get_contents($filepath));
    }
}
