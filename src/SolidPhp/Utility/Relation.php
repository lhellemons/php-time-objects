<?php

namespace SolidPhp\Utility;

use InvalidArgumentException;
use SolidPhp\ValueObjects\Value\ValueObjectTrait;

final class Relation
{
    use ValueObjectTrait;

    private const NONE = 0;
    private const LEFT = 0b001;
    private const AT_LEFT = 0b010;
    private const INSIDE = 0b011;
    private const AT_RIGHT = 0b100;
    private const RIGHT = 0b101;

    private const LEFT_SIDE = 0;
    private const RIGHT_SIDE = 3;
    private const CODE_BITS = 0b111;

    private const RELATIONS = [self::NONE, self::LEFT, self::AT_LEFT, self::INSIDE, self::AT_RIGHT, self::RIGHT];

    /** @var int */
    private $relationCode;

    private function __construct(int $code)
    {
        $this->relationCode = $code;
    }

    public static function ofSides(int $leftRelation, int $rightRelation): self
    {
        if (!in_array($leftRelation, self::RELATIONS, true)) {
            throw new InvalidArgumentException(
                sprintf('leftRelation must be one of (%s)', implode(',', self::RELATIONS))
            );
        }

        if (!in_array($rightRelation, self::RELATIONS, true)) {
            throw new InvalidArgumentException(
                sprintf('rightRelation must be one of (%s)', implode(',', self::RELATIONS))
            );
        }

        if ($leftRelation > $rightRelation) {
            throw new InvalidArgumentException('leftRelation must be less than or equal to rightRelation');
        }

        if (!$leftRelation || !$rightRelation) {
            return self::getInstance(self::NONE);
        }

        $code = ($leftRelation << self::LEFT_SIDE) | ($rightRelation << self::RIGHT_SIDE);

        return self::getInstance($code);
    }

    public static function unrelated(): self
    {
        return self::ofSides(self::NONE, self::NONE);
    }

    public static function left(): self
    {
        return self::ofSides(self::LEFT, self::LEFT);
    }

    public static function leftAdjoining(): self
    {
        return self::ofSides(self::LEFT, self::AT_LEFT);
    }

    public static function leftIntersecting(): self
    {
        return self::ofSides(self::LEFT, self::INSIDE);
    }

    public static function leftOverlapping(): self
    {
        return self::ofSides(self::LEFT, self::AT_RIGHT);
    }

    public static function overlapping(): self
    {
        return self::ofSides(self::LEFT, self::RIGHT);
    }

    public static function atLeft(): self
    {
        return self::ofSides(self::AT_LEFT, self::AT_LEFT);
    }

    public static function leftInside(): self
    {
        return self::ofSides(self::AT_LEFT, self::INSIDE);
    }

    public static function equal(): self
    {
        return self::ofSides(self::AT_LEFT, self::AT_RIGHT);
    }

    public static function rightOverlapping(): self
    {
        return self::ofSides(self::AT_LEFT, self::RIGHT);
    }

    public static function inside(): self
    {
        return self::ofSides(self::INSIDE, self::INSIDE);
    }

    public static function rightInside(): self
    {
        return self::ofSides(self::INSIDE, self::AT_RIGHT);
    }

    public static function rightIntersecting(): self
    {
        return self::ofSides(self::INSIDE, self::RIGHT);
    }

    public static function atRight(): self
    {
        return self::ofSides(self::AT_RIGHT, self::AT_RIGHT);
    }

    public static function rightAdjoining(): self
    {
        return self::ofSides(self::AT_RIGHT, self::RIGHT);
    }

    public static function right(): self
    {
        return self::ofSides(self::RIGHT, self::RIGHT);
    }

    //

    private function leftCode(): int
    {
        return ($this->relationCode & (self::CODE_BITS << self::LEFT_SIDE)) >> self::LEFT_SIDE;
    }

    private function rightCode(): int
    {
        return ($this->relationCode & (self::CODE_BITS << self::RIGHT_SIDE)) >> self::RIGHT_SIDE;
    }

    public function exists(): bool
    {
        return $this->relationCode > self::NONE;
    }

    public function equals(): bool
    {
        return $this->exists() && $this->leftCode() === self::AT_LEFT && $this->rightCode() === self::AT_RIGHT;
    }

    public function contains(): bool
    {
        return $this->exists() && $this->leftCode() <= self::AT_LEFT && $this->rightCode() >= self::AT_RIGHT;
    }

    public function isContained(): bool
    {
        return $this->exists() && $this->leftCode() > self::AT_LEFT && $this->rightCode() < self::AT_RIGHT;
    }

    public function intersects(): bool
    {
        return $this->exists() && $this->leftCode() < self::AT_RIGHT && $this->rightCode() > self::AT_LEFT;
    }

    public function isToLeft(): bool
    {
        return $this->exists() && $this->leftCode() < self::AT_LEFT;
    }

    public function liesToLeft(): bool
    {
        return $this->exists() && $this->leftCode() < self::AT_LEFT & $this->rightCode() <= self::AT_LEFT;
    }

    public function isToRight(): bool
    {
        return $this->exists() && $this->rightCode() > self::AT_RIGHT;
    }

    public function liesToRight(): bool
    {
        return $this->exists() && $this->leftCode() >= self::AT_RIGHT && $this->rightCode() > self::AT_RIGHT;
    }

    public function isLeftAdjacent(): bool
    {
        return $this->rightCode() === self::AT_LEFT;
    }

    public function isRightAdjacent(): bool
    {
        return $this->leftCode() === self::AT_RIGHT;
    }

    public function isAdjacent(): bool
    {
        return $this->isLeftAdjacent() || $this->isRightAdjacent();
    }

    public function alignsLeft(): bool
    {
        return $this->leftCode() === self::AT_LEFT;
    }

    public function alignsRight(): bool
    {
        return $this->rightCode() === self::AT_RIGHT;
    }

    public function __toString(): string
    {
        // __|||||__
        // ____=====
        //           +
        // __||###==

        static $e = '_';
        static $m = '=';
        static $o = '|';
        static $b = '#';

        if (!$this->exists()) {
            return str_repeat($e, 9);
        }

        $indices = [self::LEFT => 0, self::AT_LEFT => 2, self::INSIDE => 4, self::AT_RIGHT => 6, self::RIGHT => 8];

        $leftIndex = $indices[$this->leftCode()];
        $rightIndex = $indices[$this->rightCode()];

        $array = str_split("$e$e$o$o$o$o$o$e$e", 1);

        foreach ($array as $i => &$value) {
            if ($i >= $leftIndex &&  $i <= $rightIndex) {
                $value = ($value === $o ? $b : $m);
            }
        } unset($value);

        return implode('', $array);
    }
}
