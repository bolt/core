<?php

declare(strict_types=1);

namespace Bolt\Controller;

use Symfony\Component\HttpFoundation\Response;

/**
 * Controllers that render singular records must implement that interface.
 */
interface DetailControllerInterface
{
    public function record($slugOrId, ?string $contentTypeSlug, bool $requirePublished): Response;

    public function contentByFieldValue(string $contentTypeSlug, string $field, string $value): Response;
}
