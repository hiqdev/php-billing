<?php
/**
 * PHP Billing Library
 *
 * @link      https://github.com/hiqdev/php-billing
 * @package   php-billing
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2017-2020, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\php\billing\tests\unit\charge\modifiers;

use hiqdev\php\billing\charge\modifiers\FixedDiscount;
use hiqdev\php\billing\charge\modifiers\FullCombination;
use hiqdev\php\billing\charge\modifiers\GrowingDiscount;

/**
 * @author Andrii Vasyliev <sol@hiqdev.com>
 */
class FullCombinationTest extends \PHPUnit\Framework\TestCase
{
    public function testPlainModifiersArray_forPlainCombination()
    {
        $combination = new FullCombination(
            $a = new FixedDiscount('10.50'),
            $b = new GrowingDiscount('0.5')
        );

        $plainList = $combination->toPlainModifiersArray();
        $this->assertSame([$a, $b], $plainList);
    }

    public function testPlainModifiersArray_forRightNestedFullCombinations()
    {
        $combination = new FullCombination(
            $a = new FixedDiscount('10.50'),
            new FullCombination(
                $b = new GrowingDiscount('0.5'),
                $c = new GrowingDiscount('0.5')
            )
        );

        $plainList = $combination->toPlainModifiersArray();
        $this->assertSame([$a, $b, $c], $plainList);
    }

    public function testPlainModifiersArray_forRightDeeplyNestedFullCombinations()
    {
        $combination = new FullCombination(
            $a = new FixedDiscount('10.50'),
            new FullCombination(
                new FullCombination(
                    $b = new GrowingDiscount('0.5'),
                    $c = new GrowingDiscount('0.5')
                ),
                new FullCombination(
                    $d = new GrowingDiscount('0.5'),
                    $e = new GrowingDiscount('0.5')
                ),
            )
        );

        $plainList = $combination->toPlainModifiersArray();
        $this->assertSame([$a, $b, $c, $d, $e], $plainList);
    }

    public function testPlainModifiersArray_forDeeplyNestedFullCombinations()
    {
        $combination = new FullCombination(
            new FullCombination(
                new FullCombination(
                    $a = new GrowingDiscount('0.5'),
                    $b = new GrowingDiscount('0.5')
                ),
                new FullCombination(
                    $c = new GrowingDiscount('0.5'),
                    $d = new GrowingDiscount('0.5')
                ),
            ),
            new FullCombination(
                new FullCombination(
                    $e = new GrowingDiscount('0.5'),
                    $f = new GrowingDiscount('0.5')
                ),
                new FullCombination(
                    $g = new GrowingDiscount('0.5'),
                    $h = new GrowingDiscount('0.5')
                ),
            )
        );

        $plainList = $combination->toPlainModifiersArray();
        $this->assertSame([$a, $b, $c, $d, $e, $f, $g, $h], $plainList);
    }
}
