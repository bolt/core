<?php

declare(strict_types=1);

namespace Bolt\ComposerScripts;

use Composer\Script\Event;

class CreateProjectScript extends Script
{
    public static function execute(Event $event): void
    {
        parent::init('Running composer "post-create-project-cmd" scripts');

        self::deleteGitignore();
        self::createReadme();

        chdir(parent::getProjectFolder($event));

        self::runConsole(['bolt:reset-secret']);
        self::runConsole(['bolt:copy-themes', '--ansi']);
        if (self::isTtySupported()) {
            self::runConsole(['bolt:welcome', '--ansi']);
        }
    }

    private static function deleteGitignore(): void
    {
        if (file_exists('public/.gitignore')) {
            unlink('public/.gitignore');
        }
        if (file_exists('config/extensions/.gitignore')) {
            unlink('config/extensions/.gitignore');
        }
    }

    private static function createReadme(): void
    {
        if (file_exists('README_project.md')) {
            rename('README_project.md', 'README.md');
        }
    }
}
