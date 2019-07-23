<?php

namespace SolidPhp\Math;

use SolidPhp\ValueObjects\Value\ValueObjectTrait;

final class Fraction
{
    use ValueObjectTrait;

    /** @var int */
    private $numerator;

    /** @var int */
    private $denominator;

    public function __construct(int $numerator, int $denominator)
    {
        $this->numerator = $numerator;
        $this->denominator = $denominator;
    }

    public static function of(int $numerator, int $denominator = 1): self
    {
        $gcd = gcd($numerator, $denominator);

        return self::getInstance(intdiv($numerator, $gcd), intdiv($denominator, $gcd));
    }

    public static function ofFloat(float $source, int $precision): self
    {
        $denominator = 10 ** $precision;

        return self::of((int)($source * $denominator), $denominator);
    }

    //

    public function add(Fraction $fraction): Fraction
    {
        return self::of($this->numerator + $fraction->numerator, $this->denominator * $fraction->denominator);
    }

    public function multiply(Fraction $fraction): Fraction
    {
        return self::of($this->numerator* $fraction->numerator, $this->denominator * $fraction->denominator);
    }

    public function compare(Fraction $fraction): int
    {
        return ($this->numerator * $fraction->denominator) <=> ($fraction->numerator * $this->denominator);
    }
}

function gcd(int $a, int $b): int
{
    if ($b > $a) {[$a,$b] = [$b,$a];}

    while ($b !== 0) {
        [$a,$b] = [$b, $a % $b];
    }

    return $a;
}
