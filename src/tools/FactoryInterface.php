<?php declare(strict_types=1);

/**
 * PHP Billing Library
 *
 * @link      https://github.com/hiqdev/php-billing
 * @package   php-billing
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2017-2020, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\php\billing\tools;

use DateTimeImmutable;
use hiqdev\php\billing\action\ActionInterface;
use hiqdev\php\billing\bill\BillInterface;
use hiqdev\php\billing\charge\ChargeInterface;
use hiqdev\php\billing\customer\CustomerInterface;
use hiqdev\php\billing\plan\PlanInterface;
use hiqdev\php\billing\price\PriceInterface;
use hiqdev\php\billing\sale\SaleInterface;
use hiqdev\php\billing\target\TargetInterface;
use hiqdev\php\billing\type\TypeInterface;
use hiqdev\php\units\QuantityInterface;
use hiqdev\php\units\UnitInterface;
use Money\Currency;
use Money\Money;

/**
 * @author Andrii Vasyliev <sol@hiqdev.com>
 */
interface FactoryInterface
{
    /**
     * Create billing object by entity name and data.
     */
    public function create(string $entity, $data);

    public function getSale($data): SaleInterface;

    public function getAction($data): ActionInterface;

    public function getPlan($data): PlanInterface;

    public function getCustomer($data): CustomerInterface;

    public function getCharge($data): ChargeInterface;

    public function getPrice($data): PriceInterface;

    public function getBill($data): BillInterface;

    public function getTarget($data): TargetInterface;

    public function getType($data): TypeInterface;

    public function getUnit($data): UnitInterface;

    public function createUnit($data): UnitInterface;

    public function getQuantity($data): QuantityInterface;

    public function createQuantity($data): QuantityInterface;

    public function parseQuantity(string $str): array;

    public function getMoney($data): Money;

    public function createMoney($data): Money;

    public function parseMoney(string $str): array;

    public function getCurrency($data): Currency;

    public function createTime($data): DateTimeImmutable;

    public function getTime($data): DateTimeImmutable;
}
