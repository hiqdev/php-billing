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
use hiqdev\php\billing\sale\SaleInterface;
use hiqdev\php\billing\sale\SaleRepositoryInterface;
use hiqdev\php\billing\tests\support\sale\SimpleSaleRepository;
use hiqdev\php\billing\tests\unit\plan\PlanTest;

class SaleTest extends PlanTest
{
    /**
     * @var Sale|SaleInterface
     */
    protected $sale;
    /**
     * @var SaleRepositoryInterface|SimpleSaleRepository
     */
    protected $repository;

    protected function setUp()
    {
        parent::setUp();
        $this->time = new DateTimeImmutable('now');
        $this->sale = new Sale(null, $this->plan->verisign, $this->plan->customer, $this->plan);
        $this->repository = new SimpleSaleRepository($this->sale);
    }
}
