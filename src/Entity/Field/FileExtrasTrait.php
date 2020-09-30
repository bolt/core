<?php

declare(strict_types=1);

namespace Bolt\Entity\Field;

use Symfony\Component\Asset\PathPackage;
use Symfony\Component\Asset\VersionStrategy\EmptyVersionStrategy;
use Symfony\Component\HttpFoundation\Request;

trait FileExtrasTrait
{
    private function getPath(): ?string
    {
        if (empty($this->get('filename'))) {
            return null;
        }

        $filesPackage = new PathPackage('/files/', new EmptyVersionStrategy());

        return $filesPackage->getUrl((string) $this->get('filename'));
    }

    private function getUrl(): string
    {
        $request = Request::createFromGlobals();

        return sprintf(
            '%s://%s%s',
            $request->getScheme(),
            $this->getHost($request),
            $this->getPath()
        );
    }

    private function getHost(Request $request)
    {
        $host = $request->server->get('CANONICAL_HOST', $request->getHost());
        $scheme = $request->getScheme();
        $port = $request->getPort();

        if (($scheme === 'http' && $port === 80) || ($scheme === 'https' && $port === 443)) {
            return $host;
        }

        return $host . ':' . $port;
    }

    private function getExtension(): string
    {
        return pathinfo($this->__toString(), PATHINFO_EXTENSION);
    }
}
