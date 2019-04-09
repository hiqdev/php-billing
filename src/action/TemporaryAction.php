<?php
/**
 * PHP Billing Library
 *
 * @link      https://github.com/hiqdev/php-billing
 * @package   php-billing
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2017-2019, HiQDev (http://hiqdev.com/)
 */
namespace hiqdev\php\billing\action;

use DateTimeImmutable;
use hiqdev\php\billing\customer\CustomerInterface;
use hiqdev\php\billing\sale\SaleInterface;
use hiqdev\php\billing\target\TargetInterface;
use hiqdev\php\billing\type\TypeInterface;
use hiqdev\php\units\QuantityInterface;

/**
 * Class TemporaryAction represents an action, that is generated for
 * runtime-only purposes such as, but not limited to:
 *
 *  - Extending primary action with TemporaryActions, that represent client billing hierarchy
 *
 * Actions of this class MUST NOT be saved into the database and SHOULD be used
 * only for runtime calculations.
 *
 * @author Dmytro Naumenko <d.naumenko.a@gmail.com>
 */
class TemporaryAction extends Action
{
    private function __construct(
        $id,
        TypeInterface $type,
        TargetInterface $target,
        QuantityInterface $quantity,
        CustomerInterface $customer,
        DateTimeImmutable $time,
        SaleInterface $sale = null,
        ActionState $state = null,
        ActionInterface $parent = null
    ) {
        parent::__construct($id, $type, $target, $quantity, $customer, $time, $sale, $state, $parent);
    }

    /**
     * Creates Temporary Action out of generic $action
     *
     * @param ActionInterface $action
     * @param CustomerInterface $customer
     * @return TemporaryAction
     */
    public static function createAsSubaction(ActionInterface $action, CustomerInterface $customer): TemporaryAction
    {
        return new self(
            null,
            $action->getType(),
            $action->getTarget(),
            $action->getQuantity(),
            $customer,
            $action->getTime(),
            null,
            $action->getState(),
            $action
        );
    }
}
