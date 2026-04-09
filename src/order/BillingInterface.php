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
namespace hiqdev\php\billing\order;

use DateTimeImmutable;
use hiqdev\php\billing\action\ActionInterface;
use hiqdev\php\billing\bill\BillInterface;
use hiqdev\php\billing\charge\ChargeInterface;

/**
 * Billing calculates and saves bills for given order.
 *
 * @author Andrii Vasyliev <sol@hiqdev.com>
 */
interface BillingInterface
{
    /**
     * @param OrderInterface|ActionInterface|mixed $source
     * @return BillInterface[]
     */
    public function calculate($source, ?DateTimeImmutable $time = null): array;

    /**
     * @param OrderInterface|ActionInterface|ActionInterface[]|mixed $source
     * @return BillInterface[] array of charges
     */
    public function perform($source, ?DateTimeImmutable $time = null): array;

    /**
     * @param OrderInterface|ActionInterface|mixed $source
     * @return ChargeInterface[]
     */
    public function calculateCharges($source, ?DateTimeImmutable $time = null): array;

    public function getCalculator(): CalculatorInterface;
}
