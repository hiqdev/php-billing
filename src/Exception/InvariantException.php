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
 * Class InvariantException describes an invariant violation exception
 * during the data transformation.
 *
 * @author Dmytro Naumenko <d.naumenko.a@gmail.com>
 */
class InvariantException extends RuntimeException implements ExceptionInterface
{
}
