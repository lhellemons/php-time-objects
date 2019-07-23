<?php

namespace Test\Test\SolidPhp\TimeObjects\Range;

use SolidPhp\TimeObjects\Calendar;
use SolidPhp\TimeObjects\Range\YearRange;
use PHPUnit\Framework\TestCase;
use SolidPhp\TimeObjects\TimeObject;
use SolidPhp\Utility\Iterator;

class YearRangeTest extends TestCase
{

    public function testIterate(YearRange $range, Iterator $expectedResult): void
    {

    }

    public function getCasesForIterate(): array
    {
        $calendar = $this->createMock(Calendar::class);

        return [
            'base case' => [YearRange::infinity(), Iterator::empty()]
        ];
    }

    public function testRelationTo(YearRange $range, TimeObject $timeObject): void
    {

    }

    public function getCasesForRelationTo(): array
    {
        return [

        ];
    }
}
