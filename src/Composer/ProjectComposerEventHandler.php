<?php

namespace Bolt\Composer;

use Composer\Script\Event;


class ProjectComposerEventHandler
{
    public static function postInstall(Event $event): void
    {
        $composer = $event->getComposer();

        $config = $composer->getConfig();

        $composer->

        // Contains all the config.
        var_dump($config->all());
        die;
    }
}
