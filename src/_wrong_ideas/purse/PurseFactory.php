<?php
/**
 * PHP Billing Library
 *
 * @link      https://github.com/hiqdev/php-billing
 * @package   php-billing
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2017-2018, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\php\billing;

/**
 * @author Andrii Vasyliev <sol@hiqdev.com>
 */
class PurseFactory implements PurseFactoryInterface
{
    /**
     * Creates customer object.
     * @return Purse
     */
    public function create(PurseCreationDto $dto)
    {
        return new Purse($dto->id, $dto->currency, $dto->customer);
    }
}
