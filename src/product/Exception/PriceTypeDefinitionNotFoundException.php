<?php declare(strict_types=1);

namespace hiqdev\php\billing\product\Exception;

use hidev\exception\HasContext;
use hidev\exception\HasContextInterface;
use hiqdev\php\billing\Exception\RuntimeException;

class PriceTypeDefinitionNotFoundException extends RuntimeException implements HasContextInterface
{
    use HasContext;
}
