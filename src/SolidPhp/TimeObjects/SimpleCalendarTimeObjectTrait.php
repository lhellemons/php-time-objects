<?php

namespace SolidPhp\TimeObjects;

use SolidPhp\TimeObjects\Range\TimeRange;
use SolidPhp\Utility\Iterator;
use SolidPhp\Utility\Relation;

trait SimpleCalendarTimeObjectTrait /* implements TimeObject */
{
    use CalendarTimeObjectTrait;

    /** @var ?TimeObject */
    private $parent;

    /** @var int */
    private $sequenceNumber;

    /**
     * @param Calendar        $calendar
     * @param TimeObject|null $parent
     * @param int             $sequenceNumber
     */
    public function __construct(Calendar $calendar, ?TimeObject $parent, int $sequenceNumber)
    {
        $this->calendar = $calendar;
        $this->parent = $parent;
        $this->sequenceNumber = $sequenceNumber;
    }

    public function relationTo(TimeObject $timeObject): Relation
    {
        if ($timeObject === $this) {
            return Relation::equal();
        }

        if ($parent = $this->parent) {
            $parentRelation = $parent->relationTo($timeObject);
            if ($parentRelation->contains())
        }
    }

    public function parent(): ?TimeObject
    {
        return $this->parent;
    }

    public function children(): ?TimeRange
    {
        return $this->children ?: $this->children = $this->calendar->children($this);
    }

    public function iterate(): Iterator
    {
        $children = $this->children();

        return $children ?
            $children->iterate()
                     ->map(
                         static function (TimeObject $child) {
                             return $child->parent();
                         }
                     )->unique() :
            Iterator::create([$this]);
    }

    public function start(): ?TimeObject
    {
        $firstChild = $this->children ? $this->children->start() : null;

        return $firstChild ? $firstChild->parent() : null;
    }

    public function end(): ?TimeObject
    {
        $lastChild = $this->children ? $this->children->start() : null;

        return $lastChild ? $lastChild->parent() : null;
    }
}
