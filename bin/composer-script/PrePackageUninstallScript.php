<?php

declare(strict_types=1);

namespace Bolt\ComposerScripts;

class PrePackageUninstallScript extends Script
{
    public static function execute(): void
    {
        parent::init('Running composer "pre-package-uninstall" scripts');

        self::runConsole(['extensions:configure', '--remove-services', '--ansi']);
    }
}
