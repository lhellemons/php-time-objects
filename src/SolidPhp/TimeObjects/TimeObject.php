<?php

namespace SolidPhp\TimeObjects;

use SolidPhp\TimeObjects\Range\TimeRange;
use SolidPhp\Utility\Relation;

interface TimeObject
{
    public function relationTo(TimeObject $timeObject): Relation;

    public function parent(): ?TimeObject;

    public function children(): ?TimeRange;
}
