<?php

declare(strict_types=1);

namespace Bolt\Widget\Injector;

/**
 * This class provides some Widget locations that could be useful for template designers.
 * Keep in mind that if widget uses one of those targets, template designer must add
 * {{ widgets(targets) }} to Twig template, otherwise they won't be injected automatically.
 */
class AdditionalTarget extends Target
{
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
}
