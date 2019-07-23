<?php

namespace SolidPhp\TimeObjects\Range;

use SolidPhp\TimeObjects\Calendar;
use SolidPhp\Utility\Relation;
use SolidPhp\TimeObjects\TimeObject;
use SolidPhp\Utility\Iterator;

trait CalendarTimeRangeTrait /* implements TimeRange */
{
    /** @var CalendarTimeRange */
    private $timeRange;

    public function calendar(): Calendar
    {
        return $this->timeRange->calendar();
    }

    public function relationTo(TimeObject $timeObject): Relation
    {
        return $this->timeRange->relationTo($timeObject);
    }

    public function iterate(): Iterator
    {
        return $this->timeRange->iterate();
    }

    public function start(): ?TimeObject
    {
        return $this->timeRange->start();
    }

    public function end(): ?TimeObject
    {
        return $this->timeRange->end();
    }
}
