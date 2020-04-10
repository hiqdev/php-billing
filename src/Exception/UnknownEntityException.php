<?php

namespace hiqdev\php\billing\Exception;

use hiqdev\php\billing\ExceptionInterface;
use OutOfBoundsException;

class UnknownEntityException extends OutOfBoundsException implements ExceptionInterface
{
}
