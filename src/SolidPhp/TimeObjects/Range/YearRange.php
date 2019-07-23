<?php

namespace SolidPhp\TimeObjects\Range;

use SolidPhp\TimeObjects\Calendar;
use SolidPhp\TimeObjects\Year;

final class YearRange implements TimeRange
{
    use CalendarTimeRangeTrait;

    private function __construct(Calendar $calendar, ?Year $start, ?Year $end)
    {
        $this->timeRange = new CalendarTimeRange($calendar, $start, $end);
    }

    public static function of(Calendar $calendar, Year $start, Year $end): self
    {
        return new self($calendar, $start, $end);
    }

    public static function from(Calendar $calendar, Year $start): self
    {
        return new self($calendar, $start, null);
    }

    public static function until(Calendar $calendar, Year $end): self
    {
        return new self($calendar, null, $end);
    }

    public static function infinity(Calendar $calendar): self
    {
        return new self($calendar, null, null);
    }
}
