<?php
/**
 * PHP Billing Library
 *
 * @link      https://github.com/hiqdev/php-billing
 * @package   php-billing
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2017-2018, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\php\billing\price;

/**
 * Price factory interface.
 *
 * @author Andrii Vasyliev <sol@hiqdev.com>
 */
interface PriceFactoryInterface
{
    /**
     * Creates price object.
     */
    public function create(PriceCreationDto $dto): PriceInterface;
}
