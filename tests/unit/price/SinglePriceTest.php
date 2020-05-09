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
use hiqdev\php\billing\price\SinglePrice;
use hiqdev\php\billing\target\Target;
use hiqdev\php\billing\type\Type;
use hiqdev\php\units\Quantity;
use Money\Money;

/**
 * @author Andrii Vasyliev <sol@hiqdev.com>
 */
class SinglePriceTest extends \PHPUnit\Framework\TestCase
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

    protected function setUp()
    {
        $this->target   = new Target(1, 'server');
        $this->type     = new Type(null, 'server_traf');
        $this->quantity = Quantity::gigabyte(10);
        $this->money    = Money::USD(15);
        $this->price    = new SinglePrice(null, $this->type, $this->target, null, $this->quantity, $this->money);
    }

    protected function tearDown()
    {
    }

    public function testCalculateUsage()
    {
        $this->assertNull($this->price->calculateUsage(Quantity::byte(1)));
        $this->assertEquals(Quantity::gigabyte(90), $this->price->calculateUsage(Quantity::gigabyte(100)));
    }

    public function testCalculatePrice()
    {
        $this->assertEquals($this->money, $this->price->calculatePrice(Quantity::byte(1)));
        $this->assertEquals($this->money, $this->price->calculatePrice(Quantity::megabyte(1)));
    }

    public function testJsonSerialize()
    {
        $this->assertEquals([
            'type' => [
                'name' => 'server_traf',
            ],
            'prepaid' => [
                'unit' => 'gigabyte',
                'quantity' => 10,
            ],
            'price' => [
                'amount' => '15',
                'currency' => 'USD',
            ],
            'target' => [
                'id' => '1',
                'type' => 'server',
            ],
        ], json_decode(json_encode($this->price), true));
    }
}
