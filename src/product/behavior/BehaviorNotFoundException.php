<?php declare(strict_types=1);

namespace hiqdev\php\billing\product\behavior;

use hidev\exception\HasContext;
use hidev\exception\HasContextInterface;
use hiqdev\php\billing\Exception\RuntimeException;

class BehaviorNotFoundException extends RuntimeException implements HasContextInterface
{
    use HasContext;
}
