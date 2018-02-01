<?php
/**
 * PHP Billing Library
 *
 * @link      https://github.com/hiqdev/php-billing
 * @package   php-billing
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2017-2018, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\php\billing\tests\unit\sale;

use DateTimeImmutable;
use hiqdev\php\billing\sale\Sale;
use hiqdev\php\billing\tests\unit\plan\PlanTest;

class SaleTest extends PlanTest
{
    protected function setUp()
    {
        parent::setUp();
        $this->time = new DateTimeImmutable('now');
        $this->sale = new Sale(null, $this->plan->verisign, $this->plan->customer, $this->plan);
        $this->repository = new SimpleSaleRepository($this->sale);
    }
}
