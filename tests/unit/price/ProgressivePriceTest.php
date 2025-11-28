<?php

declare(strict_types=1);

namespace hiqdev\php\billing\tests\unit\price;

use Generator;
use hiqdev\php\billing\price\ProgressivePrice;
use hiqdev\php\billing\price\ProgressivePriceThresholdList;
use hiqdev\php\billing\target\Target;
use hiqdev\php\billing\type\Type;
use hiqdev\php\units\Quantity;
use Money\Currencies\ISOCurrencies;
use Money\Currency;
use Money\Parser\DecimalMoneyParser;
use PHPUnit\Framework\TestCase;


class ProgressivePriceTest extends TestCase
{
    private Quantity $usage;
    private DecimalMoneyParser $moneyParser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->usage = Quantity::mbps(720);
        $this->moneyParser = new DecimalMoneyParser(new ISOCurrencies());
    }

    private function createProgressivePrice(string $prepaid, string $startPrice, array $thresholdsArray): ProgressivePrice
    {
        $type = new Type('2222', 'cdn_traf95_max');
        $target = new Target('2222', 'overuse,cdn_traf95_max', 'ProgressivePrice');
        $prepaid = Quantity::mbps($prepaid);
        $money = $this->moneyParser->parse($startPrice, new Currency('EUR'));
        $thresholds = ProgressivePriceThresholdList::fromScalarsArray($thresholdsArray);

        return new ProgressivePrice('2222', $type, $target, $prepaid, $money, $thresholds);
    }

    /** @dataProvider progressivePriceProvider */
    public function testUsageIsConvertedToThresholdUnits(
        array $thresholdsArray,
        int $expectedAmount,
        string $startPrice,
        string $prepaid = '0',
        array $expectedTrace = [],
    ): void {
        $this->usage = Quantity::gbps(0.72);
        $price = $this->createProgressivePrice(
            prepaid: $prepaid,
            startPrice: $startPrice,
            thresholdsArray: $thresholdsArray
        );

        $amount = $price->calculateSum($this->usage);
        $this->assertEquals($expectedAmount, $amount->getAmount());
        $trace = array_map(fn($trace) => $trace->__toString(), $price->getCalculationTraces());
        $this->assertSame($expectedTrace, $trace);
    }

    /** @dataProvider progressivePriceProvider */
    public function testProgressivePriceCalculation(
        array $thresholdsArray,
        int $expectedAmount,
        string $startPrice,
        string $prepaid = '0',
        array $expectedTrace = [],
    ): void {
        $price = $this->createProgressivePrice(
            prepaid: $prepaid,
            startPrice: $startPrice,
            thresholdsArray: $thresholdsArray
        );

        $usage = $price->calculateUsage($this->usage);
        $this->assertSame($this->usage->getQuantity(), $usage->getQuantity());

        $amount = $price->calculateSum($this->usage);
        $this->assertEquals($expectedAmount, $amount->getAmount());

        $trace = array_map(fn($trace) => $trace->__toString(), $price->getCalculationTraces());
        $this->assertSame($expectedTrace, $trace);
    }

    /**
     * @dataProvider progressivePriceProvider
     */
    public function testProgressivePriceSerialization(
        array $inputThresholdsArray,
        int $expectedAmount,
        string $startPrice,
        string $prepaid = '0'
    ): void {
        $price = $this->createProgressivePrice(
            prepaid: $prepaid,
            startPrice: $startPrice,
            thresholdsArray: $inputThresholdsArray
        );

        $unserialized = json_decode(json_encode($price), true);
        $this->assertArrayHasKey('id', $unserialized);
        $this->assertArrayHasKey('type', $unserialized);
        $this->assertArrayHasKey('target', $unserialized);
        $this->assertArrayHasKey('thresholds', $unserialized);
        $this->assertArrayHasKey('price', $unserialized);
        $this->assertArrayHasKey('prepaid', $unserialized);

        $this->assertSame($price->getId(), $unserialized['id']);

        $this->assertSame($price->getType()->getId(), $unserialized['type']['id']);
        $this->assertSame($price->getType()->getName(), $unserialized['type']['name']);

        $this->assertSame($price->getTarget()->getId(), $unserialized['target']['id']);
        $this->assertSame($price->getTarget()->getType(), $unserialized['target']['type']);
        $this->assertSame($price->getTarget()->getName(), $unserialized['target']['name']);

        $this->assertSame($price->getPrice()->getAmount(), $unserialized['price']['amount']);
        $this->assertSame($price->getPrice()->getCurrency()->getCode(), $unserialized['price']['currency']);

        $this->assertSame($price->getPrepaid()->getQuantity(), $unserialized['prepaid']['quantity']);
        $this->assertSame($price->getPrepaid()->getUnit()->getName(), $unserialized['prepaid']['unit']);

        $thresholdsArray = $price->getThresholds()->get();
        $unserializedThresholds = array_reverse($unserialized['thresholds']);
        $this->assertCount(count($inputThresholdsArray), $unserializedThresholds);
        foreach ($thresholdsArray as $index => $threshold) {
            $val = (new \ReflectionObject($threshold))->getProperty('price')->getValue($threshold);
            $this->assertSame($val, $unserializedThresholds[$index]['price']);
            $this->assertSame($threshold->price()->getCurrency()->getCode(), $unserializedThresholds[$index]['currency']);
            $this->assertSame($threshold->quantity()->getQuantity(), $unserializedThresholds[$index]['quantity']);
            $this->assertSame($threshold->quantity()->getUnit()->getName(), $unserializedThresholds[$index]['unit']);
        }
    }

    private function progressivePriceProvider(): Generator
    {
        yield 'Simple case' => [
            'thresholds' => [
                ['price' => '0.0085', 'currency' => 'EUR', 'quantity' => '0', 'unit' => 'mbps'],
                ['price' => '0.0080', 'currency' => 'EUR', 'quantity' => '500', 'unit' => 'mbps'],
                ['price' => '0.0075', 'currency' => 'EUR', 'quantity' => '600', 'unit' => 'mbps'],
                ['price' => '0.0070', 'currency' => 'EUR', 'quantity' => '700', 'unit' => 'mbps'],
                ['price' => '0.0065', 'currency' => 'EUR', 'quantity' => '800', 'unit' => 'mbps'],
            ],
            'money' => 594,
            'price' => '0.0085',
            'prepaid' => '0',
            'trace' => [
                '0mbps * 0.0065 = 0.00',
                '20mbps * 0.0070 = 0.14',
                '100mbps * 0.0075 = 0.75',
                '100mbps * 0.0080 = 0.80',
                '500mbps * 0.0085 = 4.25',
                '0mbps * 0.01 = 0.00',
            ],
        ];

        yield 'Different prices for the same quantity – take higher price' => [
            'thresholds' => [
                ['price' => '6', 'currency' => 'EUR', 'quantity' => '0', 'unit' => 'mbps'],
                ['price' => '4', 'currency' => 'EUR', 'quantity' => '100', 'unit' => 'mbps'], // Here the qty is the same
                ['price' => '5000', 'currency' => 'EUR', 'quantity' => '0.1', 'unit' => 'gbps'], // as here, despite units are different
                ['price' => '3', 'currency' => 'EUR', 'quantity' => '200', 'unit' => 'mbps'],
            ],
            'money' => 266000,
            'price' => '6',
            'prepaid' => '0',
            'trace' => [
                '520mbps * 3 = 1,560.00',
                '0.1gbps * 5000 = 500.00',
                '0mbps * 4 = 0.00',
                '100mbps * 6 = 600.00',
                '0mbps * 6 = 0.00',
            ],
        ];

        yield 'Bill without prepaid amount' => [
            'thresholds' => [
                ['price' => '6', 'currency' => 'EUR', 'quantity' => '100', 'unit' => 'mbps'],
                ['price' => '5', 'currency' => 'EUR', 'quantity' => '200', 'unit' => 'mbps'],
                ['price' => '4', 'currency' => 'EUR', 'quantity' => '300', 'unit' => 'mbps'],
                ['price' => '3', 'currency' => 'EUR', 'quantity' => '400', 'unit' => 'mbps'],
            ],
            'money' => 306000,
            'price' => '6',
            'prepaid' => '0',
            'trace' => [
                '320mbps * 3 = 960.00',
                '100mbps * 4 = 400.00',
                '100mbps * 5 = 500.00',
                '100mbps * 6 = 600.00',
                '100mbps * 6 = 600.00',
            ],
        ];

        yield 'Bill with prepaid amount' => [
            'thresholds' => [
                ['price' => '1', 'currency' => 'EUR', 'quantity' => '20', 'unit' => 'mbps'],
                ['price' => '0.9', 'currency' => 'EUR', 'quantity' => '30', 'unit' => 'mbps'],
                ['price' => '856.00', 'currency' => 'EUR', 'quantity' => '0.1', 'unit' => 'gbps'],
                ['price' => '0.5521', 'currency' => 'EUR', 'quantity' => '130.5', 'unit' => 'mbps'],
            ],
            'result' => 43487,
            'price' => '1.03',
            'prepaid' => '10',
            'trace' => [
                '589.5mbps * 0.5521 = 325.46',
                '0.0305gbps * 856.00 = 26.11',
                '70mbps * 0.9 = 63.00',
                '10mbps * 1 = 10.00',
                '10mbps * 1.03 = 10.30',
            ],
        ];
    }

    /**
     * @dataProvider progressivePriceProviderSmallUsage
     */
    public function testProgressivePriceSmallUsage(
        array $inputThresholdsArray,
        int $expectedAmount,
        string $startPrice,
        string $prepaid = '0'
    ): void {
        $price = $this->createProgressivePrice(
            prepaid: $prepaid,
            startPrice: $startPrice,
            thresholdsArray: $inputThresholdsArray
        );
        $this->usage = Quantity::bps(6043);
        $usage = $price->calculateUsage($this->usage);
        $this->assertTrue($this->usage->equals($usage));

        $amount = $price->calculateSum($this->usage);
        $this->assertEquals($expectedAmount, $amount->getAmount());
    }

    private function progressivePriceProviderSmallUsage(): Generator
    {
        yield 'Simple case' => [
            'thresholds' => [
                ['price' => '10', 'currency' => 'EUR', 'quantity' => '0', 'unit' => 'gbps'],
            ],
            'money' => 0,
            'price' => '1',
            'prepaid' => '0',
            'trace' => [
                '6043bps * 0.00000001 = 0.00',
            ],
        ];
    }
}
