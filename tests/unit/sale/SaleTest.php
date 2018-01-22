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

use hiqdev\php\billing\action\Action;
use hiqdev\php\billing\charge\Charge;
use hiqdev\php\billing\sale\Sale;
use hiqdev\php\billing\tests\unit\plan\PlanTest;
use hiqdev\php\units\Quantity;
use hiqdev\php\units\Unit;
use Money\Money;

class SaleTest extends PlanTest
{
    protected function setUp()
    {
        parent::setUp();
        $this->sale = new Sale(null, $this->plan->verisign, $this->plan->customer, $this->plan);
        $this->repository = new SimpleSaleRepository($this->sale);
    }
}
