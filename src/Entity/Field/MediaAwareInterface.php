<?php

declare(strict_types=1);

namespace Bolt\Entity\Field;

use Bolt\Entity\Media;
use Bolt\Repository\MediaRepository;

/**
 * Field that has in excerpt must implement this interface
 */
interface MediaAwareInterface
{
    public function getLinkedMedia(MediaRepository $mediaRepository): ?Media;

    public function setLinkedMedia(MediaRepository $mediaRepository): void;
}
