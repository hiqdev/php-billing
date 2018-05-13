<?php
/**
 * PHP Billing Library
 *
 * @link      https://github.com/hiqdev/php-billing
 * @package   php-billing
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2017-2018, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\php\billing\tests\unit\charge\modifiers;

use hiqdev\php\billing\charge\modifiers\Discount;
use hiqdev\php\billing\charge\modifiers\ModifierFactory;

/**
 * @author Andrii Vasyliev <sol@hiqdev.com>
 */
class ModifierFactoryTest extends \PHPUnit\Framework\TestCase
{
    protected $factory;

    protected function setUp()
    {
        parent::setUp();
        $this->factory = new ModifierFactory();
    }

    public function testDiscount()
    {
        $discount = $this->factory->discount();
        $this->assertInstanceOf(Discount::class, $discount);

    }

    public function testNewEveryTime()
    {
        $one = $this->factory->reason('test');
        $two = $this->factory->reason('test');
        $this->assertTrue($one !== $two);
    }
}
