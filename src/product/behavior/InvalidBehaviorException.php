<?php declare(strict_types=1);

namespace hiqdev\php\billing\product\behavior;

use hidev\exception\HasContext;
use hidev\exception\HasContextInterface;
use InvalidArgumentException;

class InvalidBehaviorException extends InvalidArgumentException implements HasContextInterface
{
    use HasContext;
}
