<?php

declare(strict_types=1);

namespace Bolt\Event\Listener;

use Bolt\Entity\Field\ImageField;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Symfony\Component\HttpFoundation\RequestStack;

class ImageFieldFillListener
{
    /** @var RequestStack */
    private $requestStack;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    public function postLoad(LifecycleEventArgs $args): void
    {
        $entity = $args->getEntity();

        if ($entity instanceof ImageField) {
            $this->fillImageField($entity);
        }
    }

    public function fillImageField(ImageField $entity): void
    {
        $entity->setRequestStack($this->requestStack);
    }
}
