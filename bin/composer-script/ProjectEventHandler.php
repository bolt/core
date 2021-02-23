<?php

declare(strict_types=1);

namespace Bolt\ComposerScripts;

use Composer\Script\Event;

/**
 *  Executes various commands triggered by composer
 *  to setup and manage the Bolt project,
 *  such as: applying yaml migrations,
 *  notifying of database migrations and configuring extensions.
 */
class ProjectEventHandler
{
    public static function postCreateProject(Event $event): void
    {
        CreateProjectScript::execute($event);
    }

    /** @placeholder */
    public static function preInstall(Event $event): void
    {
    }

    public static function postInstall(Event $event): void
    {
        PostInstallScript::execute();
    }

    /** @placeholder */
    public static function preUpdate(Event $event): void
    {
    }

    public static function postUpdate(Event $event): void
    {
        PostUpdateScript::execute();
    }
}
