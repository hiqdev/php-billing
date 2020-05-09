<?php
/**
 * PHP Billing Library
 *
 * @link      https://github.com/hiqdev/php-billing
 * @package   php-billing
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2017-2020, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\php\billing\action\mux;

use hiqdev\php\billing\action\ActionInterface;

/**
 * Interface ActionMuxInterface provides an API to multiplex/demultiplex actions.
 *
 * Demultiplexing might be useful to simplify public API by passing a single action into the billing,
 * that actually represents a set of more specific action.
 *
 * For example, action "purchase a small gift in box" can be actually represented with a set of
 * the following specific actions, that should be charged:
 *
 * - purchase 1 box
 * - purchase 1 small gift
 * - purchase 1 set of packing
 * - purchase a box packing service
 *
 * @author Dmytro Naumenko <d.naumenko.a@gmail.com>
 */
interface ActionMuxInterface
{
    /**
     * Takes the $action and returns the array of demultiplexed actions.
     *
     * @param ActionInterface $action The original action
     * @return ActionInterface[] Array of demultiplexed actions
     */
    public function demultiplex(ActionInterface $action): array;

    /**
     * Takes array of actions and returns array of multiplexed actions.
     *
     * Method can return multiple actions, if the input set can not be multiplex to a single action.
     *
     * If actions can not be multiplexed, input array MUST be returned without any changes.
     *
     * @param ActionInterface[] $actions
     * @return ActionInterface[]
     */
    public function multiplex(array $actions): array;
}
