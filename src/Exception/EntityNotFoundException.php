<?php

namespace hiqdev\php\billing\Exception;

use hiqdev\php\billing\ExceptionInterface;
use OutOfBoundsException;

class EntityNotFoundException extends OutOfBoundsException implements ExceptionInterface
{
}
