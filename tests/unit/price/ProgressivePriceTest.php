<?php

declare(strict_types=1);

namespace hiqdev\php\billing\tests\unit\price;

use hiqdev\php\billing\price\MoneyBuilder;
use hiqdev\php\billing\price\ProgressivePrice;
use hiqdev\php\billing\target\Target;
use hiqdev\php\billing\type\Type;
use hiqdev\php\units\Quantity;
use Money\Money;


class ProgressivePriceTest extends \PHPUnit\Framework\TestCase
{
    /** @dataProvider progressivePriceProvider */
    public function testProgressivePrice(array $thresholds, int $resultMoney, string $startPrice)
    {
        $type = new Type('2222', 'cdn_traf95_max');
        $target = new Target('2222', 'overuse,cdn_traf95_max', 'ProgressivePrice');
        $prepaid = Quantity::mbps(0);
        $money = MoneyBuilder::buildMoney((string)$startPrice, 'EUR');
        $price = new ProgressivePrice('2222', $type, $target, $prepaid, $money, $thresholds);
        $this->assertSame(720, $price->calculateUsage(Quantity::mbps(720))->getQuantity());
        usort($thresholds, function($a, $b)
            {
                if ($b['quantity'] === $a['quantity']) {
                    return $b['price'] <=> $a['price'];
                }
                return $b['quantity'] <=> $a['quantity'];
            }
        );
        $this->assertSame(json_encode(Money::EUR($resultMoney)), json_encode($price->calculateSum(Quantity::mbps(720))));
        $this->assertSame(
            json_encode([
                'id' => '2222',
                'type' => [
                    'id' => '2222',
                    'name' => 'cdn_traf95_max',
                ],
                'target' => [
                    'id' => '2222',
                    'type' => 'overuse,cdn_traf95_max',
                    'name' => 'ProgressivePrice'
                ],
                'thresholds' => [
                    'thresholds' => $thresholds,
                ],
                'price' => [
                    "amount" => (string) (MoneyBuilder::calculatePriceMultiplier($startPrice) * $startPrice),
                    "currency" =>"EUR",
                ],
                'prepaid' => [
                    'unit' => 'mbps',
                    'quantity' => 0,
                ],
            ]),
            json_encode($price));
    }

    private function progressivePriceProvider(): \Generator
    {
        yield [
            'thresholds' => [
                ['price' => '0.0085', 'currency' => 'EUR', 'quantity' => '0', 'unit' => 'mbps',],
                ['price' => '0.0080', 'currency' => 'EUR', 'quantity' => '500', 'unit' => 'mbps',],
                ['price' => '0.0075', 'currency' => 'EUR', 'quantity' => '600', 'unit' => 'mbps',],
                ['price' => '0.0070', 'currency' => 'EUR', 'quantity' => '700', 'unit' => 'mbps',],
                ['price' => '0.0065', 'currency' => 'EUR', 'quantity' => '800', 'unit' => 'mbps',],
            ],
            'money' => 594,
            'price' => '0.0085',
        ];
        yield [
            'thresholds' => [
                ['price' => '6', 'currency' => 'EUR', 'quantity' => '0', 'unit' => 'mbps', ],
                ['price' => '5', 'currency' => 'EUR', 'quantity' => '100', 'unit' => 'mbps', ],
                ['price' => '4', 'currency' => 'EUR', 'quantity' => '100', 'unit' => 'mbps', ],
                ['price' => '3', 'currency' => 'EUR', 'quantity' => '200', 'unit' => 'mbps', ],
            ],
            'money' => 266000,
            'price' => '6',
        ];
        yield [
            'thresholds' => [
                ['price' => '6', 'currency' => 'EUR', 'quantity' => '100', 'unit' => 'mbps', ],
                ['price' => '5', 'currency' => 'EUR', 'quantity' => '200', 'unit' => 'mbps', ],
                ['price' => '4', 'currency' => 'EUR', 'quantity' => '300', 'unit' => 'mbps', ],
                ['price' => '3', 'currency' => 'EUR', 'quantity' => '400', 'unit' => 'mbps', ],
            ],
            'money' => 246000,
            'price' => '6',
        ];
    }
}
