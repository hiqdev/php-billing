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

use Exception;
use hidev\exception\HasContext;
use hidev\exception\HasContextInterface;
use hiqdev\php\billing\ExceptionInterface;
use Throwable;

/**
 * @author Andrii Vasyliev <sol@hiqdev.com>
 */
class CannotReassignException extends Exception implements ExceptionInterface, HasContextInterface
{
    use HasContext;

    public function __construct(string $message, int $code = 0, ?Throwable $previous = null)
    {
        $this->addContext(['field' => $message]);

        parent::__construct("cannot reassign $message", $code, $previous);
    }
}
