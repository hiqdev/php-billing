<?php
/**
 * PHP Billing Library
 *
 * @link      https://github.com/hiqdev/php-billing
 * @package   php-billing
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2017-2018, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\php\billing\charge;

use hiqdev\php\billing\action\ActionInterface;
use hiqdev\php\billing\price\PriceInterface;
use hiqdev\php\billing\target\TargetInterface;
use hiqdev\php\billing\type\TypeInterface;
use hiqdev\php\units\QuantityInterface;
use Money\Money;

/**
 * @author Andrii Vasyliev <sol@hiqdev.com>
 */
interface ChargeInterface extends \JsonSerializable
{
    /**
     * @return int|string
     */
    public function getId();

    /**
     * @return bool
     */
    public function hasId(): bool;

    /**
     * @return TypeInterface
     */
    public function getType(): TypeInterface;

    /**
     * @return TargetInterface
     */
    public function getTarget(): TargetInterface;

    /**
     * @return ActionInterface
     */
    public function getAction(): ActionInterface;

    /**
     * @return PriceInterface
     */
    public function getPrice(): PriceInterface;

    /**
     * @return Money
     */
    public function getSum(): Money;

    /**
     * @return QuantityInterface
     */
    public function getUsage(): QuantityInterface;

    /**
     * @param string $comment
     * @return self
     */
    public function setComment(string $comment): self;

    /**
     * @return string
     */
    public function getComment(): ?string;

    /**
     * @return self
     */
    public function setFinished(): self;

    /**
     * @return ChargeInterface|null
     */
    public function getParent(): ?ChargeInterface;

    /**
     * Provides unique string.
     * Can be used to compare or aggregate charges.
     *
     * @return string
     */
    public function getUniqueString(): string;
}
