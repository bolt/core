<?php

declare(strict_types=1);

namespace Bolt\Entity\Field;

use Bolt\Repository\MediaRepository;

/**
 * Field that has in excerpt must implement this interface
 */
interface MediaAware
{
    public function getLinkedMedia();

    public function setLinkedMedia(MediaRepository $mediaRepository);
}
