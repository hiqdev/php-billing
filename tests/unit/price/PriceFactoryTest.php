<?php
/**
 * PHP Billing Library
 *
 * @link      https://github.com/hiqdev/php-billing
 * @package   php-billing
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2017-2018, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\php\billing\tests\unit;

use hiqdev\php\billing\price\EnumPrice;
use hiqdev\php\billing\price\PriceCreationDto;
use hiqdev\php\billing\price\PriceFactory;
use hiqdev\php\billing\price\SinglePrice;
use hiqdev\php\billing\target\Target;
use hiqdev\php\billing\type\Type;
use hiqdev\php\units\Quantity;
use hiqdev\php\units\Unit;
use Money\Money;

/**
 * @author Andrii Vasyliev <sol@hiqdev.com>
 */
class PriceFactoryTest extends \PHPUnit\Framework\TestCase
{
    protected function setUp()
    {
        $this->id       = 'foo:bar';
        $this->single   = new Type(null, 'server_traf');
        $this->enum     = new Type(null, 'certificate_purchase');
        $this->target   = new Target(1, 'server');
        $this->prepaid  = Quantity::gigabyte(10);
        $this->price    = Money::USD(15);
        $this->unit     = Unit::gigabyte();
        $this->sums     = [];
        $this->factory  = new PriceFactory([
            $this->single->getName()    => SinglePrice::class,
            $this->enum->getName()      => EnumPrice::class,
            'other'                     => 'other',
        ], SinglePrice::class);
    }

    public function testEnumPrice()
    {
        $price = $this->factory->create($this->createDto([
            'id'        => $this->id,
            'type'      => $this->enum,
            'target'    => $this->target,
            'unit'      => $this->unit,
            'currency'  => $this->price->getCurrency(),
            'sums'      => $this->sums,
        ]));
        $this->assertInstanceOf(EnumPrice::class, $price);
        $this->assertSame($this->id,        $price->getId());
        $this->assertSame($this->enum,      $price->getType());
        $this->assertSame($this->target,    $price->getTarget());
        $this->assertSame($this->unit,      $price->getUnit());
        $this->assertSame($this->sums,      $price->getSums());
        $this->assertSame($this->price->getCurrency(),  $price->getCurrency());
    }

    public function testSinglePrice()
    {
        $price = $this->factory->create($this->createDto([
            'id'        => $this->id,
            'type'      => $this->single,
            'target'    => $this->target,
            'prepaid'   => $this->prepaid,
            'price'     => $this->price,
        ]));
        $this->assertInstanceOf(SinglePrice::class, $price);
        $this->assertSame($this->id,        $price->getId());
        $this->assertSame($this->single,    $price->getType());
        $this->assertSame($this->target,    $price->getTarget());
        $this->assertSame($this->prepaid,   $price->getPrepaid());
        $this->assertSame($this->price,     $price->getPrice());
    }

    public function createDto(array $data)
    {
        $dto = new PriceCreationDto();
        foreach ($data as $key => $value) {
            $dto->{$key} = $value;
        }

        return $dto;
    }
}
