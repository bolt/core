<?php

declare(strict_types=1);

namespace Bolt\Controller\Frontend;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Controllers that display a single record must implement this interface.
 */
interface DetailControllerInterface
{
    public function record(Request $request, $slugOrId, ?string $contentTypeSlug, bool $requirePublished, ?string $_locale): Response;
}
