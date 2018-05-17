<?php
/**
 * PHP Billing Library
 *
 * @link      https://github.com/hiqdev/php-billing
 * @package   php-billing
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2017-2018, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\php\billing\context;

use DateTimeImmutable;

/**
 * Context Interface.
 *
 * @author Andrii Vasyliev <sol@hiqdev.com>
 */
interface ContextInterface
{
    public function getTime(): DateTimeImmutable;
}
