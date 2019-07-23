<?php

namespace SolidPhp\TimeObjects;

use SolidPhp\TimeObjects\Range\CalendarTimeRangeTrait;
use SolidPhp\TimeObjects\Range\TimeRange;
use SolidPhp\ValueObjects\Value\ValueObjectTrait;

final class Second implements TimeRange, CalendarContainer
{
    use ValueObjectTrait;
    use CalendarTimeRangeTrait;

    /** @var int */
    private $number;

    private function __construct(Calendar $calendar, int $number)
    {
        $this->calendar = $calendar;
        $this->number = $number;
    }

    public static function of(Calendar $calendar, int $number): self
    {
        return self::getInstance($calendar, $number);
    }
}
