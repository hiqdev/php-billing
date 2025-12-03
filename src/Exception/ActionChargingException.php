<?php
declare(strict_types=1);

namespace hiqdev\php\billing\Exception;

use hidev\exception\HasContext;
use hidev\exception\HasContextInterface;
use hiqdev\php\billing\action\ActionInterface;
use Throwable;

/**
 * Class ActionChargingException should be thrown when producing charges
 * for the Action have lead to an exception.
 *
 * @author Dmytro Naumenko <d.naumenko.a@gmail.com>
 */
final class ActionChargingException extends RuntimeException implements HasContextInterface
{
    use HasContext;

    public static function forAction(ActionInterface $action, Throwable $previousException): self
    {
        if ($action->getId() !== null) {
            $message = sprintf('Failed to charge action %s: %s', $action->getId(), $previousException->getMessage());
        } else {
            $message = sprintf(
                'Failed to charge action (type: %s, target: %s, quantity: %s %s, customer: %s, time: %s): %s',
                $action->getType()->getName(), $action->getTarget()->getUniqueId(),
                $action->getQuantity()->getQuantity(), $action->getQuantity()->getUnit()->getName(),
                $action->getCustomer()->getLogin(),
                $action->getTime()->format(DATE_ATOM),
                $previousException->getMessage()
            );
        }

        $self = new self($message, 0, $previousException);
        $self->addContext(['action' => $action]);

        return $self;
    }
}
