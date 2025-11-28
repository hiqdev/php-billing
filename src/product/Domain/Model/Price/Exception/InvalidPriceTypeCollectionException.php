<?php

declare(strict_types=1);

namespace hiqdev\php\billing\product\Domain\Model\Price\Exception;

use InvalidArgumentException;

class InvalidPriceTypeCollectionException extends InvalidArgumentException
{
    public static function becauseContainsNonPriceType(mixed $value): self
    {
        $given = is_object($value) ? get_class($value) : gettype($value);

        return new self(sprintf(
            'PriceTypeCollection can only contain instances of PriceTypeInterface. Got: %s',
            $given
        ));
    }
}
