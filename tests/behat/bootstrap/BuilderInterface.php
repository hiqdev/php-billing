<?php
declare(strict_types=1);

/**
 * PHP Billing Library
 *
 * @link      https://github.com/hiqdev/php-billing
 * @package   php-billing
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2017-2020, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\php\billing\tests\behat\bootstrap;

interface BuilderInterface
{
    public function buildReseller(string $login);

    public function buildCustomer(string $login);

    public function buildPlan(string $name, string $type, bool $grouping = false);

    public function buildPrice(array $data);

    public function recreatePlan(string $name);

    public function buildSale(string $target, string $plan, string $time);

    public function buildPurchase(string $target, string $plan, string $time);

    public function findBills(array $data): array;

    public function performBilling(string $time): void;

    public function setAction(string $type, int $amount, string $unit, string $target, string $time): void;

    public function performCalculation(string $time): array;
}
