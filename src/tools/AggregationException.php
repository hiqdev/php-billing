<?php
/**
 * PHP Billing Library
 *
 * @link      https://github.com/hiqdev/php-billing
 * @package   php-billing
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2017-2020, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\php\billing\charge;

use hiqdev\php\billing\ExceptionInterface;

/**
 * @author Andrii Vasyliev <sol@hiqdev.com>
 */
class AggregationException extends \RuntimeException implements ExceptionInterface
{
}
