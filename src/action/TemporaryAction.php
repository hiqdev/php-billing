<?php
/**
 * PHP Billing Library
 *
 * @link      https://github.com/hiqdev/php-billing
 * @package   php-billing
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2017-2020, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\php\billing\action;

use hiqdev\php\billing\customer\CustomerInterface;

/**
 * Class TemporaryAction represents an action, that is generated for
 * runtime-only purposes such as, but not limited to:
 *
 * - Extending primary action with TemporaryActions, that represent client billing hierarchy
 * - Actions produced in ActionMux
 *
 * Actions of this class MUST NOT be saved into the database and SHOULD be used
 * only for runtime calculations.
 *
 * @author Dmytro Naumenko <d.naumenko.a@gmail.com>
 */
class TemporaryAction extends Action implements TemporaryActionInterface
{
    /**
     * Creates Temporary Action out of generic $action
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
