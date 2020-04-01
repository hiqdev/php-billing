<?php

namespace hiqdev\php\billing\Exception;

use hiqdev\php\billing\ExceptionInterface;
use Exception;
use Throwable;

/**
 * @author Andrii Vasyliev <sol@hiqdev.com>
 */
class CannotReassignException extends Exception implements ExceptionInterface
{
    private $field;

    public function __construct(string $message, int $code = 0, Throwable $previous = null)
    {
        $this->field = $message;
        parent::__construct("cannot reassign $message", $code, $previous);
    }

    public function getField()
    {
        return $this->field;
    }
}
