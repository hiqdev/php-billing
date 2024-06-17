<?php

declare(strict_types=1);

namespace hiqdev\php\billing\tests\unit\price;

use hiqdev\php\billing\price\ProgressivePrice;
use hiqdev\php\billing\price\ProgressivePriceThresholdsDto;
use hiqdev\php\billing\target\Target;
use hiqdev\php\billing\type\Type;
use hiqdev\php\units\Quantity;
use Money\Money;
use Money\Currency;

class ProgressivePriceTest extends \PHPUnit\Framework\TestCase
{

    protected ProgressivePrice $price;

    protected Target $target;

    protected Type $type;

    protected Quantity $prepaid;



    protected function setUp(): void
    {
        $this->target = new Target('2222', 'overuse,cdn_traf95_max', 'progressive');
        $this->type = new Type('2222', 'cdn_traf95_max');
        $this->prepaid = Quantity::mbps(0);
        $this->price = new ProgressivePrice('2222', $this->type, $this->target, $this->prepaid, $this->getThresholds());
    }

    protected function tearDown(): void
    {
    }

    public function testCalculateUsage()
    {
        $this->assertEquals(720, $this->price->calculateUsage(Quantity::mbps(720))->getQuantity());
    }

    public function testCalculatePrice()
    {
        $this->assertSame(json_encode(Money::EUR(594)), json_encode($this->price->calculatePrice(Quantity::mbps(720))));
    }

    public function testJsonSerialize()
    {
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
                    'name' => 'progressive'
                ],
                'thresholds' => $this->getThresholds(),
                'prepaid' => [
                    'unit' => 'mbps',
                    'quantity' => 0,
                ],
            ]),
            json_encode($this->price));
    }

    private function getThresholds()
    {
        return [
            new ProgressivePriceThresholdsDto(0.0085, new Currency('EUR'), 0),
            new ProgressivePriceThresholdsDto(0.0080, new Currency('EUR'), 500),
            new ProgressivePriceThresholdsDto(0.0075, new Currency('EUR'), 600),
            new ProgressivePriceThresholdsDto(0.0070, new Currency('EUR'), 700),
            new ProgressivePriceThresholdsDto(0.0060, new Currency('EUR'), 800),
        ];
    }
}
