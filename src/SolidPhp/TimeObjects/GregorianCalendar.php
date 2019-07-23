<?php

namespace SolidPhp\TimeObjects;

use SolidPhp\TimeObjects\Range\TimeRange;
use SolidPhp\Utility\Relation;

class GregorianCalendar implements Calendar
{

    public function contains(TimeObject $timeObject): bool
    {
        return $timeObject instanceof CalendarContainer && $timeObject->calendar() === $this;
    }

    public function relate(TimeObject $a, TimeObject $b): Relation
    {

    }

    public function next(TimeObject $timeObject, int $quantity = 1): ?TimeObject
    {
        throw new \RuntimeException(
            sprintf('Method %s not implemented yet.', __METHOD__)
        ); // TODO: Implement next() method.
    }

    public function children(TimeObject $timeObject): ?TimeRange
    {
        throw new \RuntimeException(
            sprintf('Method %s not implemented yet.', __METHOD__)
        ); // TODO: Implement children() method.
    }

    public function parent(TimeObject $timeObject): ?TimeObject
    {
        throw new \RuntimeException(
            sprintf('Method %s not implemented yet.', __METHOD__)
        ); // TODO: Implement parent() method.
    }

    /**
     * @param mixed $source
     *
     * @return TimeObject
     * @throws \InvalidArgumentException
     */
    public function of($source): TimeObject
    {
        throw new \RuntimeException(
            sprintf('Method %s not implemented yet.', __METHOD__)
        ); // TODO: Implement of() method.
    }

    public function serialize(TimeObject $timeObject): string
    {
        throw new \RuntimeException(
            sprintf('Method %s not implemented yet.', __METHOD__)
        ); // TODO: Implement serialize() method.
    }
}
