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

use hidev\exception\HasContext;
use hidev\exception\HasContextInterface;
use hiqdev\php\billing\charge\ChargeInterface;
use hiqdev\php\billing\ExceptionInterface;
use Exception;

final class ChargeOverlappingException extends Exception implements ExceptionInterface, HasContextInterface
{
    use HasContext;

    public static function forCharge(ChargeInterface $charge): self
    {
        $self = new self(sprintf(
            'Charge %s being saved overlaps a previously saved one. Unique key: %s',
            $charge->getId(),
            $charge->getUniqueString()
        ));
        $self->addContext([
            'chargeTypeName' => $charge->getType()->getName(),
            'chargeActionTime' => $charge->getAction()->getTime()->format(DATE_ATOM),
        ]);

        return $self;
    }
}
