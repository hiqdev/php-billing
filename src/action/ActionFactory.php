<?php
/**
 * PHP Billing Library
 *
 * @link      https://github.com/hiqdev/php-billing
 * @package   php-billing
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2017-2020, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\php\billing\action;

/**
 * Default action factory.
 *
 * @author Andrii Vasyliev <sol@hiqdev.com>
 */
class ActionFactory implements ActionFactoryInterface
{
    public function create(ActionCreationDto $dto): AbstractAction
    {
        return new Action(
            $dto->id,
            $dto->type,
            $dto->target,
            $dto->quantity,
            $dto->customer,
            $dto->time,
            $dto->sale,
            $dto->state,
            $dto->parent,
        );
    }
}
