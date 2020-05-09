<?php
/**
 * PHP Billing Library
 *
 * @link      https://github.com/hiqdev/php-billing
 * @package   php-billing
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2017-2020, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\php\billing\Exception;

use hiqdev\php\billing\ExceptionInterface;
use OutOfBoundsException;

class EntityNotFoundException extends OutOfBoundsException implements ExceptionInterface
{
}
