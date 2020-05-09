<?php
/**
 * PHP Billing Library
 *
 * @link      https://github.com/hiqdev/php-billing
 * @package   php-billing
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2017-2020, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\php\billing\customer;

/**
 * Default customer factory.
 *
 * @author Andrii Vasyliev <sol@hiqdev.com>
 */
class CustomerFactory implements CustomerFactoryInterface
{
    /**
     * Creates customer object.
     * @return Customer
     */
    public function create(CustomerCreationDto $dto)
    {
        return new Customer($dto->id, $dto->login, $dto->seller);
    }
}
