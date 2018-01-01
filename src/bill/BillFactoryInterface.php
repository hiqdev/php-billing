<?php
/**
 * PHP Billing Library
 *
 * @link      https://github.com/hiqdev/php-billing
 * @package   php-billing
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2017-2018, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\php\billing\bill;

/**
 * Bill factory interface.
 *
 * @author Andrii Vasyliev <sol@hiqdev.com>
 */
interface BillFactoryInterface
{
    /**
     * Creates bill object.
     * @return Bill
     */
    public function create(BillCreationDto $dto);
}
