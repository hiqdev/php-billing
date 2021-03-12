<?php
declare(strict_types=1);

/**
 * PHP Billing Library
 *
 * @link      https://github.com/hiqdev/php-billing
 * @package   php-billing
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2017-2021, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\php\billing\Exception;

use hiqdev\php\billing\ExceptionInterface;

/**
 * Class ConstraintException describes an integrity constraint violation exception
 *
 * @author Dmytro Naumenko <d.naumenko.a@gmail.com>
 */
class ConstraintException extends RuntimeException implements ExceptionInterface
{
}
