<?php
/**
 *
 *
 * @author Bob den Otter <bobdenotter@gmail.com>
 */

namespace Bolt\Snippets;


class WeatherWidget extends BaseWidget
{
    protected $name = 'Weather Widget';
    protected $type = 'widget';
    protected $target = Target::WIDGET_BACK_EDITCONTENT_ASIDE_TOP;
    protected $priority = 100;

    public function invoke()
    {
        return "This is the weather, man!";
    }
}