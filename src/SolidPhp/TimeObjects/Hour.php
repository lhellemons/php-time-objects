<?php

namespace SolidPhp\TimeObjects;

use SolidPhp\ValueObjects\Value\ValueObjectTrait;

final class Hour
{
    use ValueObjectTrait;
    use CalendarTimeObjectTrait;

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
