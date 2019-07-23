<?php

namespace Test\SolidPhp\TimeObjects;

use PHPUnit\Framework\TestCase;
use SolidPhp\Utility\Relation;

class RelationTest extends TestCase
{
    /**
     * @param Relation  $relation
     * @param QuerySpec $expected
     *
     * @dataProvider getCasesForQueryMethods
     */
    public function testQueryMethods(Relation $relation, QuerySpec $expected): void
    {
        $this->assertMeetsSpec($relation, $expected, 'exists');
        $this->assertMeetsSpec($relation, $expected, 'equals');
        $this->assertMeetsSpec($relation, $expected, 'contains');
        $this->assertMeetsSpec($relation, $expected, 'isContained');
        $this->assertMeetsSpec($relation, $expected, 'intersects');
        $this->assertMeetsSpec($relation, $expected, 'isToLeft');
        $this->assertMeetsSpec($relation, $expected, 'liesToLeft');
        $this->assertMeetsSpec($relation, $expected, 'isToRight');
        $this->assertMeetsSpec($relation, $expected, 'liesToRight');
        $this->assertMeetsSpec($relation, $expected, 'isLeftAdjacent');
        $this->assertMeetsSpec($relation, $expected, 'isRightAdjacent');
    }

    private function assertMeetsSpec(Relation $relation, QuerySpec $spec, string $claim): void
    {
        $expectedResult = $spec->$claim;
        $actualResult = $relation->$claim();
        $failMessage = sprintf(
            'Claim for relation "%s" ("%s") not met: %s expected to be "%s" but was "%s"',
            $relation,
            $this->dataDescription(),
            $claim,
            var_export($expectedResult, true),
            var_export($actualResult, true)
        );
        $this->assertEquals($expectedResult, $actualResult, $failMessage);
    }

    public function getCasesForQueryMethods(): array
    {
        // * exists
        // e equals
        // c contains
        // c isContained
        // i intersects
        // l isToLeft
        // L liesToLeft
        // r isToRight
        // R liesToRight
        // l isLeftAdjacent
        // r isRightAdjacent
        return [
            //                                                                                    reccilLrRlr
            'unrelated'          => [Relation::unrelated(),                  QuerySpec::ofPattern('___________')],
            'left'               => [Relation::left(),                            QuerySpec::ofPattern('*____lL____')],
            'left adjoining'     => [Relation::leftAdjoining(),         QuerySpec::ofPattern('*____lL__l_')],
            'left intersecting'  => [Relation::leftIntersecting(),   QuerySpec::ofPattern('*___il_____')],
            'left overlapping'   => [Relation::leftOverlapping(), QuerySpec::ofPattern('*_c_il_____')],
            'overlapping'        => [Relation::overlapping(), QuerySpec::ofPattern('*_c_il_r___')],
            'at left'            => [Relation::atLeft(), QuerySpec::ofPattern('*________l_')],
            'left inside'        => [Relation::leftInside(),               QuerySpec::ofPattern('*___i______')],
            'equal'              => [Relation::equal(),                          QuerySpec::ofPattern('*ec_i______')],
            'right overlapping'  => [Relation::rightOverlapping(), QuerySpec::ofPattern('*_c_i__r___')],
            'inside'             => [Relation::inside(),                        QuerySpec::ofPattern('*__ci______')],
            'right inside'       => [Relation::rightInside(),             QuerySpec::ofPattern('*___i______')],
            'right intersecting' => [Relation::rightIntersecting(), QuerySpec::ofPattern('*___i__r___')],
            'at right'           => [Relation::atRight(), QuerySpec::ofPattern('*_________r')],
            'right adjoining'    => [Relation::rightAdjoining(), QuerySpec::ofPattern('*______rR_r')],
            'right'              => [Relation::right(),                          QuerySpec::ofPattern('*______rR__')],
        ];
    }

    /**
     * @param Relation $relation
     * @param string   $expectedOutput
     * @dataProvider getCasesForToString
     */
    public function testToString(Relation $relation, string $expectedOutput): void
    {
        $this->assertEquals($expectedOutput, (string)$relation);
    }

    public function getCasesForToString(): array
    {
        return [
            //                                                       __|||||__
            'unrelated'          => [Relation::unrelated(),                  '_________'],
            'left'               => [Relation::left(), '=_|||||__'],
            'left adjoining'     => [Relation::leftAdjoining(), '==#||||__'],
            'left intersecting'  => [Relation::leftIntersecting(),   '==###||__'],
            'left overlapping'   => [Relation::leftOverlapping(),     '==#####__'],
            'overlapping'        => [Relation::overlapping(),              '==#####=='],
            'at left'            => [Relation::atLeft(),                       '__#||||__'],
            'left inside'        => [Relation::leftInside(),               '__###||__'],
            'equal'              => [Relation::equal(),                          '__#####__'],
            'right overlapping'  => [Relation::rightOverlapping(),   '__#####=='],
            'inside'             => [Relation::inside(),                        '__||#||__'],
            'right inside'       => [Relation::rightInside(),             '__||###__'],
            'right intersecting' => [Relation::rightIntersecting(), '__||###=='],
            'at right'           => [Relation::atRight(),                     '__||||#__'],
            'right adjoining'    => [Relation::rightAdjoining(), '__||||#=='],
            'right'              => [Relation::right(),                          '__|||||_='],
        ];
    }
}

class QuerySpec
{
    /** @var bool */public $exists;
    /** @var bool */public $equals;
    /** @var bool */public $contains;
    /** @var bool */public $isContained;
    /** @var bool */public $intersects;
    /** @var bool */public $isToLeft;
    /** @var bool */public $liesToLeft;
    /** @var bool */public $isToRight;
    /** @var bool */public $liesToRight;
    /** @var bool */public $isLeftAdjacent;
    /** @var bool */public $isRightAdjacent;

    public function __construct(
        bool $exists,
        bool $equals,
        bool $contains,
        bool $isContained,
        bool $intersects,
        bool $isToLeft,
        bool $liesToLeft,
        bool $isToRight,
        bool $liesToRight,
        bool $isLeftAdjacent,
        bool $isRightAdjacent
    ) {
        $this->exists = $exists;
        $this->equals = $equals;
        $this->contains = $contains;
        $this->isContained = $isContained;
        $this->intersects = $intersects;
        $this->isToLeft = $isToLeft;
        $this->liesToLeft = $liesToLeft;
        $this->isToRight = $isToRight;
        $this->liesToRight = $liesToRight;
        $this->isLeftAdjacent = $isLeftAdjacent;
        $this->isRightAdjacent = $isRightAdjacent;
    }

    public static function ofPattern(string $pattern, $falseChar = '_'): self
    {
        $pattern = substr(str_pad($pattern, 11,$falseChar), 0, 11);

        return new self(
            $pattern[0] !== $falseChar,
            $pattern[1] !== $falseChar,
            $pattern[2] !== $falseChar,
            $pattern[3] !== $falseChar,
            $pattern[4] !== $falseChar,
            $pattern[5] !== $falseChar,
            $pattern[6] !== $falseChar,
            $pattern[7] !== $falseChar,
            $pattern[8] !== $falseChar,
            $pattern[9] !== $falseChar,
            $pattern[10] !== $falseChar
        );
    }
}
