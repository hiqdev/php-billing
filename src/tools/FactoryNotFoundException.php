<?php

namespace hiqdev\php\billing\tools;

use hiqdev\php\billing\ExceptionInterface;
use OutOfBoundsException;

class FactoryNotFoundException extends OutOfBoundsException implements ExceptionInterface
{
}
