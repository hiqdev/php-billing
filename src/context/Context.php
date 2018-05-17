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
 * Context.
 *
 * @author Andrii Vasyliev <sol@hiqdev.com>
 */
class Context implements ContextInterface
{
    public function __construct(DateTimeImmutable $time = null)
    {
        if ($time === null) {
            $time = new DateTimeImmutable();
        }
        $this->time = $time;
    }

    public function getTime(): DateTimeImmutable
    {
        return $this->time;
    }
}
