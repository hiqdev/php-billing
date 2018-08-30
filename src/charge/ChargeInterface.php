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
use hiqdev\php\units\Quantity;
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
     * @return ActionInterface
     */
    public function getAction();

    /**
     * @return PriceInterface
     */
    public function getPrice();

    /**
     * @return Money
     */
    public function getSum();

    /**
     * @return Quantity
     */
    public function getUsage();

    /**
     * @param string $comment
     * @return self
     */
    public function setComment(string $comment): self;

    /**
     * @return self
     */
    public function setFinished(): self;

    /**
     * @return ChargeInterface|null
     */
    public function getParent(): ?ChargeInterface;
}
