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

use DateTimeImmutable;
use hiqdev\php\billing\action\Action;
use hiqdev\php\billing\action\ActionInterface;
use hiqdev\php\billing\charge\Charge;
use hiqdev\php\billing\charge\ChargeInterface;
use hiqdev\php\billing\charge\ChargeModifier;
use hiqdev\php\billing\charge\modifiers\FixedDiscount;
use hiqdev\php\billing\charge\modifiers\FullCombination;
use hiqdev\php\billing\charge\modifiers\GrowingDiscount;
use hiqdev\php\billing\charge\modifiers\MonthlyCap;
use hiqdev\php\billing\customer\Customer;
use hiqdev\php\billing\target\Target;
use hiqdev\php\billing\target\TargetInterface;
use hiqdev\php\billing\type\Type;
use hiqdev\php\units\Quantity;
use Money\Currency;
use Money\Money;
use PHPUnit\Framework\TestCase;

/**
 * @author Andrii Vasyliev <sol@hiqdev.com>
 */
class FullCombinationTest extends TestCase
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

    public function testChargeParentPreservesObjectLinks(): void
    {
        $combination = new FullCombination(
            $a = new FixedDiscount('120 USD'),
            $b = new MonthlyCap('28 days'),
        );

        $charge = new Charge(
            null,
            Type::anyId('monthly'),
            new Target(TargetInterface::ANY, 'vps'),
            $action = new Action(
                null,
                Type::anyId('monthly'),
                new Target(TargetInterface::ANY, 'vps'),
                Quantity::create('items', 1),
                new Customer(1, 'test'),
                new DateTimeImmutable('2022-05-01 00:00:00'),
            ),
            null,
            Quantity::create('items', 1),
            new Money(240_00, new Currency('USD')),
        );

        $result = $combination->modifyCharge($charge, $action);
        $this->assertCount(2, $result);
        $this->assertSame('12000', $result[0]->getSum()->getAmount());
        $this->assertSame($result[1]->getParent(), $result[0], 'Parent should reference the first charge');
    }

    public function testRightModifierIsIgnoredWhenLeftModifierRemovesOriginalCharge(): void
    {
        $combination = new FullCombination(
            new MonthlyCap('28 days'),
            new class implements ChargeModifier {
                public function modifyCharge(?ChargeInterface $charge, ActionInterface $action): array
                {
                    throw new \RuntimeException('Should not be called');
                }

                public function isSuitable(?ChargeInterface $charge, ActionInterface $action): bool
                {
                    return true;
                }
            },
        );

        $charge = new Charge(
            null,
            Type::anyId('monthly'),
            new Target(TargetInterface::ANY, 'vps'),
            $action = new Action(
                null,
                Type::anyId('monthly'),
                new Target(TargetInterface::ANY, 'vps'),
                Quantity::create('items', 1),
                new Customer(1, 'test'),
                new DateTimeImmutable('2022-05-01 00:00:00'),
            ),
            null,
            Quantity::create('items', 1),
            new Money(240_00, new Currency('USD')),
        );

        $result = $combination->modifyCharge($charge, $action);
        $this->assertCount(2, $result);
        $this->assertSame('24000', $result[0]->getSum()->getAmount());
        $this->assertSame($result[1]->getParent(), $result[0], 'Parent should reference the first charge');
    }
}
