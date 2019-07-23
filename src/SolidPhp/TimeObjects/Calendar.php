<?php

namespace SolidPhp\TimeObjects;

use SolidPhp\TimeObjects\Range\TimeRange;
use SolidPhp\Utility\Relation;

interface Calendar
{
    public function contains(TimeObject $timeObject): bool;

    public function relate(TimeObject $a, TimeObject $b): Relation;

    public function next(TimeObject $timeObject, int $quantity = 1): ?TimeObject;

    public function children(TimeObject $timeObject): ?TimeRange;

    public function parent(TimeObject $timeObject): ?TimeObject;

    /**
     * @param mixed $source
     *
     * @return TimeObject
     * @throws \InvalidArgumentException
     */
    public function of($source): TimeObject;

    public function serialize(TimeObject $timeObject): string;

//    // generic level
//
//    public function years(): YearRange;
//    public function months(): MonthRange;
//    public function days(): DayRange;
//    public function hours(): HourRange;
//    public function minutes(): MinuteRange;
//    public function seconds(): SecondRange;
//    public function milliseconds(): MillisecondRange;
//
//    // year-level
//
//    public function yearMonths(Year $year): YearMonthRange;
//    public function yearDays(Year $year): YearDayRange;
//    public function yearHours(Year $year): YearHourRange;
//    public function yearMinutes(Year $year): YearMinuteRange;
//    public function yearSeconds(Year $year): YearSecondRange;
//    public function yearMilliseconds(Year $year): YearMillisecondRange;
//
//    // month-level
//
//    public function monthDays(Month $month): MonthDayRange;
//    public function monthHours(Month $month): MonthHourRange;
//    public function monthMinutes(Month $month): MonthMinuteRange;
//    public function monthSeconds(Month $month): MonthSecondRange;
//    public function monthMilliseconds(Month $month): MonthMillisecondRange;
//
//    // day-level
//
//    public function dayHours(Day $day): DayHourRange;
//    public function dayMinutes(Day $day): DayMinuteRange;
//    public function daySeconds(Day $day): DaySecondRange;
//    public function dayMilliseconds(Day $day): DayMillisecondRange;
//
//    // hour-level
//
//    public function hourMinutes(Hour $hour): HourMinuteRange;
//    public function hourSeconds(Hour $hour): HourSecondRange;
//    public function hourMilliseconds(Hour $hour): HourMillisecondRange;
//
//    // minute-level
//
//    public function minuteSeconds(Minute $minute): MinuteSecondRange;
//    public function minuteMilliseconds(Minute $minute): MinuteMillisecondRange;
//
//    // second-level
//
//    public function secondMilliseconds(Second $second): SecondMillisecondRange;

}
