<?php

declare(strict_types=1);

namespace hiqdev\php\billing\tests\unit\price;

use Generator;
use hiqdev\php\billing\Money\MultipliedMoney;
use hiqdev\php\billing\price\ProgressivePriceThreshold;
use hiqdev\php\units\Quantity;
use Money\Currency;
use Money\Money;
use PHPUnit\Framework\TestCase;

/**
 * Class ProgressivePriceThresholdTest
 *
 * @author Dmytro Naumenko <d.naumenko.a@gmail.com>
 * @covers \hiqdev\php\billing\price\ProgressivePriceThreshold
 */
class ProgressivePriceThresholdTest extends TestCase
{
    /**
     * @dataProvider scalarsDataProvider
     */
    public function testCreateFromScalar(string $price, string $currency, string $quantity, string $unit): void
    {
        $threshold = ProgressivePriceThreshold::createFromScalar($price, $currency, $quantity, $unit);
        $this->assertSame($price, $threshold->getRawPrice());
        $this->assertSame($unit, $threshold->quantity()->getUnit()->getName());
        $this->assertSame($unit, $threshold->unit()->getName());
        $this->assertSame($quantity, $threshold->quantity()->getQuantity());
        $this->assertSame([
            'price' => $price,
            'currency' => $currency,
            'quantity' => $quantity,
            'unit' => $unit,
        ], $threshold->__toArray());

        $this->assertSame($currency, $threshold->price()->getCurrency()->getCode());
        $this->assertInstanceOf(MultipliedMoney::class, $threshold->price());

        $this->assertSame(json_encode($threshold->__toArray()), json_encode($threshold));
    }

    public function testCreateFromObjects(): void
    {
        $threshold = ProgressivePriceThreshold::createFromObjects(
            new Money(1022, new Currency('USD')),
            Quantity::create('items', 2)
        );

        $this->assertSame([
            'price' => '10.22',
            'currency' => 'USD',
            'quantity' => '2',
            'unit' => 'items',
        ], $threshold->__toArray());
    }

    public function scalarsDataProvider(): Generator
    {
        yield ['10', 'USD', '20', 'gpbs'];
        yield ['10.24', 'EUR', '0', 'items'];
    }
}
