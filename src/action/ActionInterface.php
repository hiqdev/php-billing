<?php
/**
 * PHP Billing Library
 *
 * @link      https://github.com/hiqdev/php-billing
 * @package   php-billing
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2017-2018, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\php\billing\action;

use DateTimeImmutable;
use hiqdev\php\billing\charge\ChargeInterface;
use hiqdev\php\billing\customer\CustomerInterface;
use hiqdev\php\billing\price\PriceInterface;
use hiqdev\php\billing\sale\SaleInterface;
use hiqdev\php\billing\target\Target;
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
interface ActionInterface extends \JsonSerializable
{
    /**
     * Returns if the given price applicable to this action.
     *
     * @param PriceInterface $price
     * @return bool
     */
    public function isApplicable(PriceInterface $price): bool;

    /**
     * Returns client ot this action.
     * @return CustomerInterface
     */
    public function getCustomer(): CustomerInterface;

    /**
     * Returns target ot this action.
     * @return TargetInterface
     */
    public function getTarget(): TargetInterface;

    /**
     * Returns type ot this action.
     * @return TypeInterface
     */
    public function getType(): TypeInterface;

    /**
     * Returns quantity ot this action.
     * @return QuantityInterface
     */
    public function getQuantity(): QuantityInterface;

    /**
     * Returns time ot this action.
     * @return DateTimeImmutable
     */
    public function getTime(): DateTimeImmutable;

    /**
     * Returns sale if set.
     * @return SaleInterface|null
     */
    public function getSale(): ?SaleInterface;

    /**
     * Returns null if the action state is not set.
     * @return bool|null
     */
    public function isFinished(): ?bool;

    /**
     * @return ActionInterface|null
     */
    public function getParent(): ?ActionInterface;

    /**
     * @return ActionState|null
     */
    public function getState(): ?ActionState;
}
