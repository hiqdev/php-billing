<?php declare(strict_types=1);

namespace hiqdev\php\billing\product\invoice;

use hidev\exception\HasContext;
use hidev\exception\HasContextInterface;
use InvalidArgumentException;

class InvalidRepresentationException extends InvalidArgumentException implements HasContextInterface
{
    use HasContext;
}
