<?php

namespace SolidPhp\TimeObjects;

use SolidPhp\TimeObjects\Range\TimeRange;
use SolidPhp\Utility\Iterator;
use SolidPhp\ValueObjects\Value\ValueObjectTrait;

final class Year implements TimeRange, CalendarContainer
{
    use ValueObjectTrait;
    use SimpleCalendarTimeObjectTrait;

    /** @var int */
    private $number;

    /** @var TimeRange|null */
    private $children;

    public static function of(Calendar $calendar, int $number): self
    {
        return self::getInstance($calendar, null, $number);
    }

    public function parent(): ?TimeObject
    {
        return null;
    }
}
