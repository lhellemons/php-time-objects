<?php

namespace SolidPhp\TimeObjects\Range;

use SolidPhp\TimeObjects\TimeObject;
use SolidPhp\Utility\Iterator;

interface TimeRange extends TimeObject
{
    public function iterate(): Iterator;

    public function start(): ?TimeObject;

    public function end(): ?TimeObject;
}
