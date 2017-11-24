<?php
/**
 * PHP Billing Library
 *
 * @link      https://github.com/hiqdev/php-billing
 * @package   php-billing
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2017, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\php\billing\action;

/**
 * Action factory interface.
 *
 * @author Andrii Vasyliev <sol@hiqdev.com>
 */
interface ActionFactoryInterface
{
    /**
     * Creates action object.
     * @return Action
     */
    public function create(ActionCreationDto $dto);
}
