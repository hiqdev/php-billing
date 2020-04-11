<?php
/**
 * PHP Billing Library
 *
 * @link      https://github.com/hiqdev/php-billing
 * @package   php-billing
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2017-2018, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\php\billing\tests\support\tools;

use hiqdev\php\billing\action\ActionFactory;
use hiqdev\php\billing\customer\CustomerFactory;
use hiqdev\php\billing\plan\PlanFactory;
use hiqdev\php\billing\price\PriceFactory;
use hiqdev\php\billing\price\SinglePrice;
use hiqdev\php\billing\sale\SaleFactory;
use hiqdev\php\billing\target\TargetFactory;
use hiqdev\php\billing\tools\Factory;
use hiqdev\php\billing\type\TypeFactory;
use hiqdev\php\billing\bill\BillFactory;

class SimpleFactory extends Factory
{
    public function __construct(array $factories = [])
    {
        return parent::__construct(array_merge(self::simpleFactories(), $factories));
    }

    public static function simpleFactories(): array
    {
        return [
            'action'    => new ActionFactory(),
            'bill'      => new BillFactory(),
            'customer'  => new CustomerFactory(),
            'plan'      => new PlanFactory(),
            'price'     => new PriceFactory([], SinglePrice::class),
            'sale'      => new SaleFactory(),
            'target'    => new TargetFactory(),
            'type'      => new TypeFactory(),
        ];
    }
}
