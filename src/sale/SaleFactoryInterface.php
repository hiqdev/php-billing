<?php
/**
 * PHP Billing Library
 *
 * @link      https://github.com/hiqdev/php-billing
 * @package   php-billing
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2017-2018, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\php\billing\sale;

/**
 * Sale factory interface.
 *
 * @author Andrii Vasyliev <sol@hiqdev.com>
 */
interface SaleFactoryInterface
{
    /**
     * Creates sale object.
     * @return Sale
     */
    public function create(SaleCreationDto $dto);
}
