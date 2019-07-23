<?php

namespace SolidPhp\TimeObjects;

use SolidPhp\Utility\Relation;

trait CalendarTimeObjectTrait
{
    /** @var Calendar */
    private $calendar;

    public function calendar(): Calendar
    {
        return $this->calendar;
    }

    public function relationTo(TimeObject $timeObject): Relation
    {
        return $this->calendar->relate($this, $timeObject);
    }

    public function __toString(): string
    {
        return $this->calendar->serialize($this);
    }
}
