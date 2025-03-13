<?php

declare(strict_types=1);

namespace Bolt\Widget\Injector;

use ReflectionClass;

/**
 * This class categorizes all possible automatic widget locations.
 */
class Target
{
    // works even without valid html
    public const BEFORE_HTML = 'beforecontent';

    public const AFTER_HTML = 'aftercontent';

    public const BEFORE_CONTENT = 'beforecontent';

    public const AFTER_CONTENT = 'aftercontent';

    // unpredictable
    public const BEFORE_CSS = 'beforecss';

    public const AFTER_CSS = 'aftercss';

    public const BEFORE_JS = 'beforejs';

    public const AFTER_JS = 'afterjs';

    public const AFTER_META = 'aftermeta';

    // main structure
    public const START_OF_HEAD = 'startofhead';

    public const END_OF_HEAD = 'endofhead';

    public const START_OF_BODY = 'startofbody';

    public const END_OF_BODY = 'endofbody';

    public const END_OF_HTML = 'endofhtml';

    // substructure
    public const BEFORE_HEAD_META = 'beforeheadmeta';

    public const AFTER_HEAD_META = 'afterheadmeta';

    public const BEFORE_HEAD_CSS = 'beforeheadcss';

    public const AFTER_HEAD_CSS = 'afterheadcss';

    public const BEFORE_HEAD_JS = 'beforeheadjs';

    public const AFTER_HEAD_JS = 'afterheadjs';

    public const BEFORE_BODY_CSS = 'beforebodycss';

    public const AFTER_BODY_CSS = 'afterbodycss';

    public const BEFORE_BODY_JS = 'beforebodyjs';

    public const AFTER_BODY_JS = 'afterbodyjs';

    // this one goes nowhere in html
    public const NOWHERE = 'nowhere';

    public function listAll(): array
    {
        $reflection = new ReflectionClass($this);

        return $reflection->getConstants();
    }
}
