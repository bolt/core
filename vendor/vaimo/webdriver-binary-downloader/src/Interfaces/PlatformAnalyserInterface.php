<?php
/**
 * Copyright © Vaimo Group. All rights reserved.
 * See LICENSE_VAIMO.txt for license details.
 */
namespace Vaimo\WebDriverBinaryDownloader\Interfaces;

interface PlatformAnalyserInterface
{
    const TYPE_LINUX32 = 'linux32';
    const TYPE_LINUX64 = 'linux64';
    const TYPE_WIN32 = 'win32';
    const TYPE_WIN64 = 'win64';
    const TYPE_MAC64 = 'mac64';
    const TYPE_FREEBSD64 = 'unix64';
    const TYPE_FREEBSD32 = 'unix32';

    /**
     * @return string
     */
    public function getPlatformCode();

    /**
     * @return string
     */
    public function getPlatformName();
}
