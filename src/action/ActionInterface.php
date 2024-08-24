<?php
/**
 * PHP Billing Library
 *
 * @link      https://github.com/hiqdev/php-billing
 * @package   php-billing
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2017-2020, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\php\billing\action;

use DateTimeImmutable;
use hiqdev\php\billing\customer\CustomerInterface;
use hiqdev\php\billing\EntityInterface;
use hiqdev\php\billing\price\PriceInterface;
use hiqdev\php\billing\sale\SaleInterface;
use hiqdev\php\billing\target\TargetInterface;
use hiqdev\php\billing\type\TypeInterface;
use hiqdev\php\units\QuantityInterface;

/**
 * Action is action to be charged.
 *
 * Provides:
 *
 * - data: id, type, target, quantity, customer, time, sale, state, parent
 * - logic:
 *      - unique ID
 *      - check is applicable to price
 *
 * @author Andrii Vasyliev <sol@hiqdev.com>
 */
interface ActionInterface extends EntityInterface
{
    /**
     * Returns if the given price applicable to this action.
     */
    public function isApplicable(PriceInterface $price): bool;

    /**
     * Returns client ot this action.
     */
    public function getCustomer(): CustomerInterface;

    /**
     * Returns target ot this action.
     */
    public function getTarget(): TargetInterface;

    /**
     * Returns type ot this action.
     */
    public function getType(): TypeInterface;

    /**
     * Returns quantity ot this action.
     */
    public function getQuantity(): QuantityInterface;

    /**
     * Returns time ot this action.
     */
    public function getTime(): DateTimeImmutable;

    /**
     * Returns sale if set.
     */
    public function getSale(): ?SaleInterface;

    /**
     * Returns null if the action state is not set.
     */
    public function isNotActive(): ?bool;

    public function getParent(): ?ActionInterface;

    public function getState(): ?ActionState;

    public function hasSale();

    public function setSale(SaleInterface $sale);

    public function getUsageInterval(): UsageInterval;
}
