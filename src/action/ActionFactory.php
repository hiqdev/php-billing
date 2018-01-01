<?php
/**
 * PHP Billing Library
 *
 * @link      https://github.com/hiqdev/php-billing
 * @package   php-billing
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2017-2018, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\php\billing\action;

/**
 * Default action factory.
 *
 * @author Andrii Vasyliev <sol@hiqdev.com>
 */
class ActionFactory implements ActionFactoryInterface
{
    /**
     * Creates action object.
     * @return Action
     */
    public function create(ActionCreationDto $dto)
    {
        return new Action($dto->id, $dto->name, $dto->seller, $dto->prices ?: []);
    }
}
