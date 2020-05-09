<?php
/**
 * PHP Billing Library
 *
 * @link      https://github.com/hiqdev/php-billing
 * @package   php-billing
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2017-2020, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\php\billing\plan;

use hiqdev\php\billing\customer\CustomerInterface;
use hiqdev\php\billing\EntityInterface;
use hiqdev\php\billing\price\PriceInterface;

/**
 * Plan Interface.
 *
 * @author Andrii Vasyliev <sol@hiqdev.com>
 */
interface PlanInterface extends EntityInterface
{
    /**
     * @return int|string
     */
    public function getId();

    /**
     * Globally unique ID.
     * @return int|string
     */
    public function getUniqueId();

    /**
     * @return PriceInterface[]
     */
    public function getPrices(): ?array;

    /**
     * @return CustomerInterface
     */
    public function getSeller(): ?CustomerInterface;
}
