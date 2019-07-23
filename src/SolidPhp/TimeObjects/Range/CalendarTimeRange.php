<?php

namespace SolidPhp\TimeObjects\Range;

use SolidPhp\TimeObjects\Calendar;
use SolidPhp\TimeObjects\CalendarTimeObjectTrait;
use SolidPhp\TimeObjects\TimeObject;
use SolidPhp\Utility\Iterator;

class CalendarTimeRange implements TimeRange
{
    use CalendarTimeObjectTrait;

    /** @var TimeObject|null */
    private $start;

    /** @var TimeObject|null */
    private $end;

    public function __construct(Calendar $calendar, ?TimeObject $start, ?TimeObject $end)
    {
        $this->calendar = $calendar;
        $this->start = $start;
        $this->end = $end;
    }

    public function iterate(): Iterator
    {
        if (!$this->start) {
            return Iterator::empty();
        }

        $iterator = Iterator::recurse(
            function (TimeObject $current) {
                return $this->calendar->next($current, 1);
            }
        );

        if ($this->end) {
            return $iterator->takeWhile(
                function (?TimeObject $current) {
                    return $current && !$this->calendar->relate($current, $this->end)->isToRight();
                }
            );
        }

        return $iterator;
    }

    public function start(): ?TimeObject
    {
        return $this->start;
    }

    public function end(): ?TimeObject
    {
        return $this->end;
    }
}
