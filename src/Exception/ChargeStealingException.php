<?php
declare(strict_types=1);
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

/**
 * ChargeStealingException should be thrown, when a charge being persisted,
 * tries to override a reference from one Bill to another.
 *
 * @author Dmytro Naumenko <d.naumenko.a@gmail.com>
 */
final class ChargeStealingException extends Exception implements ExceptionInterface, HasContextInterface
{
    use HasContext;

    private ChargeInterface $charge;

    public static function fromPdoException(ChargeInterface $charge, Exception $exception): self
    {
        $self = new self(sprintf(
            'Charge being saved tries to steal an existing charge from another bill: %s',
            self::trimExceptionMessage($exception->getMessage())
        ));
        $self->addContext(['charge' => $charge]);

        return $self;
    }

    private static function trimExceptionMessage(string $message): string
    {
        return preg_replace('/^.+ERROR:\s+([^\n]+).+$/s', '$1', $message);
    }
}
