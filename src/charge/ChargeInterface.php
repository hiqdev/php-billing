<?php
/**
 * PHP Billing Library
 *
 * @link      https://github.com/hiqdev/php-billing
 * @package   php-billing
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2017-2020, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\php\billing\charge;

use hiqdev\php\billing\action\ActionInterface;
use hiqdev\php\billing\bill\BillInterface;
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

    public function hasId(): bool;

    public function setId($id): ChargeInterface;

    public function getType(): TypeInterface;

    public function getTarget(): TargetInterface;

    public function getAction(): ActionInterface;

    public function getPrice(): ?PriceInterface;

    public function getSum(): Money;

    public function getBill(): ?BillInterface;

    public function getUsage(): QuantityInterface;

    public function setComment(string $comment): self;

    /**
     * @return string
     */
    public function getComment(): ?string;

    public function setFinished(): self;

    public function getParent(): ?ChargeInterface;

    public function overwriteParent(ChargeInterface $parent): self;

    /**
     * Provides unique string.
     * Can be used to compare or aggregate charges.
     */
    public function getUniqueString(): string;
}
