<?php

declare(strict_types=1);

namespace Bolt\Snippet;

use ReflectionClass;

/**
 * Bolt Snippet target location.
 *
 * This class categorizes all possible snippet locations in constants.
 */
class Target
{
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
    public const AFTER_HTML = 'afterhtml';

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

    public const WIDGET_FRONT_MAIN_TOP = 'main_top';
    public const WIDGET_FRONT_MAIN_BREAK = 'main_break';
    public const WIDGET_FRONT_MAIN_BOTTOM = 'main_bottom';
    public const WIDGET_FRONT_ASIDE_TOP = 'aside_top';
    public const WIDGET_FRONT_ASIDE_MIDDLE = 'aside_middle';
    public const WIDGET_FRONT_ASIDE_BOTTOM = 'aside_bottom';
    public const WIDGET_FRONT_FOOTER = 'footer';

    public const WIDGET_BACK_DASHBOARD_ASIDE_TOP = 'dashboard_aside_top';
    public const WIDGET_BACK_DASHBOARD_ASIDE_MIDDLE = 'dashboard_aside_middle';
    public const WIDGET_BACK_DASHBOARD_ASIDE_BOTTOM = 'dashboard_aside_bottom';
    public const WIDGET_BACK_DASHBOARD_BELOW_HEADER = 'dashboard_below_header';
    public const WIDGET_BACK_DASHBOARD_BOTTOM = 'dashboard_bottom';
    public const WIDGET_BACK_OVERVIEW_ASIDE_TOP = 'overview_aside_top';
    public const WIDGET_BACK_OVERVIEW_ASIDE_MIDDLE = 'overview_aside_middle';
    public const WIDGET_BACK_OVERVIEW_ASIDE_BOTTOM = 'overview_aside_bottom';
    public const WIDGET_BACK_OVERVIEW_BELOW_HEADER = 'overview_below_header';
    public const WIDGET_BACK_OVERVIEW_BOTTOM = 'overview_bottom';
    public const WIDGET_BACK_EDITCONTENT_ASIDE_TOP = 'editcontent_aside_top';
    public const WIDGET_BACK_EDITCONTENT_ASIDE_MIDDLE = 'editcontent_aside_middle';
    public const WIDGET_BACK_EDITCONTENT_ASIDE_BOTTOM = 'editcontent_aside_bottom';
    public const WIDGET_BACK_EDITCONTENT_BELOW_HEADER = 'editcontent_below_header';
    public const WIDGET_BACK_EDITCONTENT_BOTTOM = 'editcontent_bottom';
    public const WIDGET_BACK_FILES_BELOW_HEADER = 'files_below_header';
    public const WIDGET_BACK_FILES_BOTTOM = 'files_bottom';
    public const WIDGET_BACK_EDITFILE_BELOW_HEADER = 'editfile_below_header';
    public const WIDGET_BACK_EDITFILE_BOTTOM = 'editfile_bottom';
    public const WIDGET_BACK_LOGIN_TOP = 'login_top';
    public const WIDGET_BACK_LOGIN_MIDDLE = 'login_middle';
    public const WIDGET_BACK_LOGIN_BOTTOM = 'login_bottom';

    public const NOWHERE = 'nowhere';

    /**
     * Returns all possible target locations (which are constants).
     */
    public function listAll(): array
    {
        $reflection = new ReflectionClass($this);

        return $reflection->getConstants();
    }
}
