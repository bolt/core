<?php

namespace Bolt\Api;

use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;

class ContentDataPersister implements ContextAwareDataPersisterInterface
{
    /** @var ContextAwareDataPersisterInterface */
    private $decorated;

    public function __construct(ContextAwareDataPersisterInterface $decorated)
    {
        $this->decorated = $decorated;
    }

    public function supports($data, array $context = []): bool
    {
        return $this->decorated->supports($data, $context);
    }

    public function persist($data, array $context = [])
    {
        // Here we need to make some adjustments.
        // Like setting the proper author, making the fields
        // the right type, etc.
        dd("PERSISTING");

        $this->decorated->persist($data, $context);
    }

    public function remove($data, array $context = [])
    {
        $this->decorated->persist($data, $context);
    }
}
