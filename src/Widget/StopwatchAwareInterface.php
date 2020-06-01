<?php

declare(strict_types=1);

namespace Bolt\Widget;

use Symfony\Component\Stopwatch\Stopwatch;

/**
 * Interface StopwatchAwareInterface - Widgets that implement this interface, will have
 * their execution time show up in Symfony's Profiler
 */
interface StopwatchAwareInterface extends WidgetInterface
{
    public function startStopwatch(Stopwatch $stopwatch);

    public function stopStopwatch();
}
