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
 * Action factory interface.
 *
 * @author Andrii Vasyliev <sol@hiqdev.com>
 */
interface ActionFactoryInterface
{
    public function create(ActionCreationDto $dto): AbstractAction;
}
