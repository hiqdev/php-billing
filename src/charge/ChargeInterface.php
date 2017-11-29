<?php
/**
 * PHP Billing Library
 *
 * @link      https://github.com/hiqdev/php-billing
 * @package   php-billing
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2017, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\php\billing\charge;

use hiqdev\php\billing\action\ActionInterface;
use hiqdev\php\billing\price\PriceInterface;

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
}
