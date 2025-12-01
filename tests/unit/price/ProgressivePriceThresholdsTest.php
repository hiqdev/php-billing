<?php

declare(strict_types=1);

namespace hiqdev\php\billing\tests\unit\price;

use hiqdev\php\billing\price\ProgressivePriceThreshold;
use hiqdev\php\billing\price\ProgressivePriceThresholdList;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

/**
 * Class ProgressivePriceThresholdsTest
 *
 * @author Dmytro Naumenko <d.naumenko.a@gmail.com>
 */
#[\PHPUnit\Framework\Attributes\CoversClass(\hiqdev\php\billing\price\ProgressivePriceThresholdList::class)]
class ProgressivePriceThresholdsTest extends TestCase
{
    public function testCreateFromScalarsArray(): void
    {
        $thresholds = ProgressivePriceThresholdList::fromScalarsArray([
            [
                'price' => '10.22',
                'currency' => 'USD',
                'quantity' => '2',
                'unit' => 'items',
            ],
            [
                'price' => '9.0994',
                'currency' => 'USD',
                'quantity' => '10',
                'unit' => 'items',
            ],
        ]);

        $this->assertCount(2, $thresholds->get());
    }

    public function testWithAdded(): void
    {
        $thresholds = new ProgressivePriceThresholdList([
            ProgressivePriceThreshold::createFromScalar(
                '10.22',
                'USD',
                '2',
                'items'
            ),
            ProgressivePriceThreshold::createFromScalar(
                '9.0994',
                'USD',
                '10',
                'items'
            ),
        ]);

        $newThreshold = ProgressivePriceThreshold::createFromScalar(
            '8.0994',
            'USD',
            '20',
            'items'
        );

        $newThresholds = $thresholds->withAdded($newThreshold);

        $this->assertCount(3, $newThresholds->get());
        $this->assertNotSame($thresholds, $newThresholds);
        $this->assertNotContains($newThreshold, $thresholds->get());
    }

    public function testDifferentCurrency(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Progressive price thresholds must have the same currency');
        new ProgressivePriceThresholdList([
            ProgressivePriceThreshold::createFromScalar(
                '10.22',
                'USD',
                '2',
                'items'
            ),
            ProgressivePriceThreshold::createFromScalar(
                '9.0994',
                'EUR',
                '10',
                'items'
            ),
        ]);
    }

    public function testUnitsConvertabilityIsChecked(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Progressive price thresholds must be of the same unit family');
        new ProgressivePriceThresholdList([
            ProgressivePriceThreshold::createFromScalar(
                '10.22',
                'USD',
                '2',
                'items'
            ),
            ProgressivePriceThreshold::createFromScalar(
                '9.0994',
                'USD',
                '10',
                'kg'
            ),
        ]);
    }

    public function testToArray(): void
    {
        $thresholds = new ProgressivePriceThresholdList([
            ProgressivePriceThreshold::createFromScalar(
                '10.22',
                'USD',
                '2',
                'items'
            ),
            ProgressivePriceThreshold::createFromScalar(
                '9.0994',
                'USD',
                '10',
                'items'
            ),
        ]);

        $this->assertSame([
            [
                'price' => '10.22',
                'currency' => 'USD',
                'quantity' => '2',
                'unit' => 'items',
            ],
            [
                'price' => '9.0994',
                'currency' => 'USD',
                'quantity' => '10',
                'unit' => 'items',
            ],
        ], $thresholds->__toArray());
    }

    public function testJsonSerializable(): void
    {
        $thresholds = new ProgressivePriceThresholdList([
            ProgressivePriceThreshold::createFromScalar(
                '10.22',
                'USD',
                '2',
                'items'
            ),
            ProgressivePriceThreshold::createFromScalar(
                '9.0994',
                'USD',
                '10',
                'items'
            ),
        ]);

        $this->assertSame([
            [
                'price' => '10.22',
                'currency' => 'USD',
                'quantity' => '2',
                'unit' => 'items',
            ],
            [
                'price' => '9.0994',
                'currency' => 'USD',
                'quantity' => '10',
                'unit' => 'items',
            ],
        ], json_decode(json_encode($thresholds), true));
    }
}
