<?php

namespace Bolt\ComposerScripts;

class PrePackageUninstallScript extends Script
{
    public static function execute(): void
    {
        parent::init('Running composer "pre-package-uninstall" scripts');

        self::run('php bin/console extensions:configure --remove-services --ansi');
    }
}
