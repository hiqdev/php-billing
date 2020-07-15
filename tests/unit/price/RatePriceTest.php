<?php
/**
 * PHP Billing Library
 *
 * @link      https://github.com/hiqdev/php-billing
 * @package   php-billing
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2017-2020, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\php\billing\tests\unit\price;

use hiqdev\php\billing\action\Action;
use hiqdev\php\billing\price\RatePrice;
use hiqdev\php\billing\target\Target;
use hiqdev\php\billing\type\Type;
use hiqdev\php\units\Quantity;
use Money\Money;

/**
 * @author Andrii Vasyliev <sol@hiqdev.com>
 */
class RatePriceTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var SinglePrice
     */
    protected $price;

    /**
     * @var Action
     */
    protected $action;

    /**
     * @var Money
     */
    protected $money;

    protected function setUp(): void
    {
        $this->target   = new Target(1, 'client');
        $this->type     = new Type(null, 'referral');
        $this->sum      = 123;
        $this->rate     = 5;
        $this->quantity = Quantity::usd($this->sum/$this->rate);
        $this->price    = new RatePrice(null, $this->type, $this->target, null, $this->rate);
    }

    protected function tearDown(): void
    {
    }

    public function testCalculateUsage()
    {
        $this->assertEquals($this->quantity, $this->price->calculateUsage($this->quantity));
    }

    public function testCalculateSum()
    {
        $this->assertEquals(Money::USD($this->sum), $this->price->calculateSum($this->quantity));
    }

    public function testJsonSerialize()
    {
        $this->assertEquals([
            'type' => [
                'name' => 'referral',
            ],
            'rate' => 5,
            'target' => [
                'id' => '1',
                'type' => 'client',
            ],
        ], json_decode(json_encode($this->price), true));
    }
}
