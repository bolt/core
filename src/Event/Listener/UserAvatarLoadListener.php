<?php

declare(strict_types=1);

namespace Bolt\Event\Listener;

use Bolt\Collection\DeepCollection;
use Bolt\Configuration\Config;
use Bolt\Entity\User;
use Doctrine\ORM\Event\PostLoadEventArgs;

class UserAvatarLoadListener
{
    /** @var DeepCollection */
    private $avatarConfig;

    public function __construct(Config $config)
    {
        /** @var DeepCollection $config */
        $config = $config->get('general');
        $this->avatarConfig = $config->get('user_avatar');
    }

    public function postLoad(PostLoadEventArgs $args): void
    {
        $entity = $args->getObject();

        if ($entity instanceof User) {
            if (! $entity->getAvatar() && $this->avatarConfig->get('default_avatar') !== '') {
                $entity->setAvatar($this->avatarConfig->get('default_avatar'));
            }
        }
    }
}
